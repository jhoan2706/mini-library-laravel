<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $loans = Loan::query()
            ->with(['copy.book', 'user'])
            ->when(!$user->hasRole(['admin', 'librarian']), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('copy.book', function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('author', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('loans.index', compact('loans'));
    }
}