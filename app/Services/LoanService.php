<?php

// app/Services/LoanService.php

namespace App\Services;

use App\Events\BookCheckedIn;
use App\Events\BookCheckedOut;
use App\Exceptions\CopyNotAvailableException;
use App\Models\Copy;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    private const LOAN_PERIOD_DAYS = 14;

    public function checkOut(Copy $copy, User $borrower): Loan
    {
        return DB::transaction(function () use ($copy, $borrower) {
            $lockedCopy = Copy::whereKey($copy->id)->lockForUpdate()->firstOrFail();

            // ✅ 1. VERIFICAR QUE LA COPIA NO ESTÉ PRESTADA
            $hasActiveLoan = Loan::where('copy_id', $lockedCopy->id)
                ->where('status', 'active')
                ->exists();

            if ($hasActiveLoan) {
                throw new CopyNotAvailableException(
                    "El ejemplar {$lockedCopy->barcode} ya está prestado."
                );
            }

            // ✅ 2. VERIFICAR QUE EL USUARIO NO TENGA OTRO PRÉSTAMO DEL MISMO LIBRO
            $userHasLoanForSameBook = Loan::where('user_id', $borrower->id)
                ->where('status', 'active')
                ->whereHas('copy', function ($q) use ($lockedCopy) {
                    $q->where('book_id', $lockedCopy->book_id);
                })
                ->exists();

            if ($userHasLoanForSameBook) {
                throw new \Exception("El usuario ya tiene un préstamo activo de este libro.");
            }

            $loan = Loan::create([
                'copy_id' => $lockedCopy->id,
                'user_id' => $borrower->id,
                'due_date' => Carbon::now()->addDays(self::LOAN_PERIOD_DAYS),
                'status' => 'active',
            ]);

            event(new BookCheckedOut($loan));

            return $loan;
        });
    }

    public function checkIn(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loan->update([
                'status' => 'returned',
                'checked_in_at' => now(),
            ]);

            event(new BookCheckedIn($loan));

            return $loan->fresh();
        });
    }
}
