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
        // Locking pesimista: evita que dos requests concurrentes presten
        // la misma copia al mismo tiempo (condición de carrera real en
        // cualquier sistema de biblioteca con más de un bibliotecario).
        return DB::transaction(function () use ($copy, $borrower) {
            $lockedCopy = Copy::whereKey($copy->id)->lockForUpdate()->firstOrFail();

            $hasActiveLoan = Loan::where('copy_id', $lockedCopy->id)
                ->where('status', 'active')
                ->exists();

            if ($hasActiveLoan) {
                throw new CopyNotAvailableException(
                    "El ejemplar {$lockedCopy->barcode} ya está prestado."
                );
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
