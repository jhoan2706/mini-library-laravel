<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Copy;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookWebController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $books = Book::query()
            ->with(['copies.loans.user'])
            ->when($search, fn ($query) => $query->search($search))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard', compact('books', 'search'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()->can('books.create'), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'copies_count' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'genre' => $validated['genre'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'synopsis' => $validated['synopsis'] ?? null,
        ]);

        $copies = [];
        for ($i = 0; $i < $validated['copies_count']; $i++) {
            $copies[] = [
                'id' => (string) Str::uuid(),
                'barcode' => 'LIB' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                'condition' => 'good',
            ];
        }

        $book->copies()->createMany($copies);

        return redirect()->route('dashboard')->with('success', 'Book added successfully.');
    }

    public function edit(Book $book)
    {
        abort_unless(Auth::user()->can('books.update'), 403);

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        abort_unless(Auth::user()->can('books.update'), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255', 'unique:books,isbn,' . $book->id],
            'synopsis' => ['nullable', 'string'],
        ]);

        $book->update($validated);

        return redirect()->route('dashboard')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        abort_unless(Auth::user()->can('books.delete'), 403);

        $hasActiveLoans = $book->copies()
            ->whereHas('loans', fn ($query) => $query->where('status', 'active'))
            ->exists();

        if ($hasActiveLoans) {
            return redirect()->route('dashboard')->with('error', 'No se puede eliminar un libro con préstamos activos.');
        }

        $book->delete();

        return redirect()->route('dashboard')->with('success', 'Book deleted successfully.');
    }

    public function checkout(Book $book, LoanService $loanService)
    {
        abort_unless(Auth::user()->can('loans.checkout'), 403);

        $copy = $book->copies->first(fn (Copy $copy) => $copy->loans->where('status', 'active')->isEmpty());

        if (! $copy) {
            return redirect()->route('dashboard')->with('error', 'No hay copias disponibles para prestar.');
        }

        try {
            $loanService->checkOut($copy, Auth::user());
        } catch (\Exception $e) {
            Log::warning('Checkout failed', ['book_id' => $book->id, 'error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', 'No se pudo prestar la copia.');
        }

        return redirect()->route('dashboard')->with('success', 'Copia prestada correctamente.');
    }

    public function checkin(Loan $loan, LoanService $loanService)
    {
        abort_unless(Auth::user()->can('loans.checkin'), 403);

        $loanService->checkIn($loan);

        return redirect()->route('dashboard')->with('success', 'Préstamo devuelto correctamente.');
    }
}
