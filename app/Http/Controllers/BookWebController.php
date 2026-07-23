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
use App\Models\User; // ✅ Agregar esta línea

class BookWebController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $books = Book::query()
            ->with(['copies.loans.user'])
            ->when($search, fn($query) => $query->search($search))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $users = \App\Models\User::all(); // ✅ IMPORTANTE

        return view('dashboard', compact('books', 'search', 'users'));
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
            ->whereHas('loans', fn($query) => $query->where('status', 'active'))
            ->exists();

        if ($hasActiveLoans) {
            return redirect()->route('dashboard')->with('error', 'No se puede eliminar un libro con préstamos activos.');
        }

        $book->delete();

        return redirect()->route('dashboard')->with('success', 'Book deleted successfully.');
    }

    public function checkout(Book $book, Request $request, LoanService $loanService)
    {
        abort_unless(Auth::user()->can('loans.checkout'), 403);

        $user = Auth::user();

        // Si es admin o librarian, puede seleccionar a quién prestar
        if ($user->hasRole(['admin', 'librarian'])) {
            $request->validate([
                'user_id' => ['required', 'exists:users,id'],
            ]);
            $borrower = \App\Models\User::findOrFail($request->user_id);
        } else {
            // Si es member, solo puede prestar a sí mismo
            $borrower = $user;
        }

        $copy = $book->copies->first(fn(Copy $copy) => $copy->loans->where('status', 'active')->isEmpty());

        if (! $copy) {
            return redirect()->route('dashboard')->with('error', 'No hay copias disponibles para prestar.');
        }

        try {
            $loanService->checkOut($copy, $borrower);
        } catch (\App\Exceptions\CopyNotAvailableException $e) {
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::warning('Checkout failed', ['book_id' => $book->id, 'error' => $e->getMessage()]);
            return redirect()->route('dashboard')->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', "Copia prestada a {$borrower->name} correctamente.");
    }

    public function checkin(Loan $loan, LoanService $loanService)
    {
        $user = Auth::user();

        // ✅ Solo admin, librarian o el dueño del préstamo pueden devolver
        if (! $user->hasRole(['admin', 'librarian']) && $loan->user_id !== $user->id) {
            abort(403, 'No tienes permiso para devolver este préstamo.');
        }

        $loanService->checkIn($loan);

        return redirect()->route('dashboard')->with('success', 'Préstamo devuelto correctamente.');
    }

    public function show(Book $book)
    {
        $book->load(['copies.loans.user']);
        return view('books.show', compact('book'));
    }
}
