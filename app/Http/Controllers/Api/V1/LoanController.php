<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\CopyNotAvailableException;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function __construct(private LoanService $loans) {}

    public function checkOut(Request $request, Copy $copy)
    {
        $this->authorize('checkout', Loan::class);

        $borrower = $request->user()->hasRole(['admin', 'librarian']) && $request->filled('user_id')
            ? User::findOrFail($request->input('user_id'))
            : $request->user();

        try {
            $loan = $this->loans->checkOut($copy, $borrower);
        } catch (CopyNotAvailableException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json($loan->load('copy.book'), 201);
    }

    public function checkIn(Request $request, Loan $loan)
    {
        $this->authorize('checkin', $loan); // solo admin/librarian, vía Gate::define
        $loan = $this->loans->checkIn($loan);

        return response()->json($loan->load('copy.book'));
    }
}
