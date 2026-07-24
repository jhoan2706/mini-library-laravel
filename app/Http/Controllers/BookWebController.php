<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Copy;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

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

        $users = \App\Models\User::all();

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
            'published_at' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'synopsis' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'copies_count' => ['required', 'integer', 'min:1', 'max:20'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'genre' => $validated['genre'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'synopsis' => $validated['synopsis'] ?? null,
            'tags' => $validated['tags'] ? explode(',', $validated['tags']) : null,
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('books', 'public');
            $book->update(['cover_image' => $path]);
        }

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
            'published_at' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'synopsis' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:255'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            'additional_copies' => ['nullable', 'integer', 'min:0', 'max:20'],
        ]);

        

        // Subir imagen
        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && Storage::exists('public/' . $book->cover_image)) {
                Storage::delete('public/' . $book->cover_image);
            }
            $path = $request->file('cover_image')->store('books', 'public');
            $validated['cover_image'] = $path;
        }

        // Convertir tags a array
        if (isset($validated['tags']) && $validated['tags']) {
            $validated['tags'] = explode(',', $validated['tags']);
        } else {
            $validated['tags'] = null;
        }


        // Actualizar libro
        $book->update($validated);

        // CREAR NUEVAS COPIAS
        if (isset($validated['additional_copies']) && $validated['additional_copies'] > 0) {
            $copies = [];
            for ($i = 0; $i < $validated['additional_copies']; $i++) {
                $copies[] = [
                    'id' => (string) Str::uuid(),
                    'barcode' => 'LIB' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                    'condition' => 'good',
                ];
            }
            $book->copies()->createMany($copies);
        }

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

        // Solo admin, librarian o el dueño del préstamo pueden devolver
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

    public function showCheckinForm(Loan $loan)
    {
        $user = Auth::user();

        // Solo el dueño del préstamo puede ver el formulario
        if ($loan->user_id !== $user->id) {
            abort(403, 'No tienes permiso para devolver este préstamo.');
        }

        return view('loans.checkin', compact('loan'));
    }

    public function processCheckin(Request $request, Loan $loan, LoanService $loanService)
    {
        $user = Auth::user();

        if ($loan->user_id !== $user->id) {
            abort(403, 'No tienes permiso para devolver este préstamo.');
        }

        $validated = $request->validate([
            'review' => ['nullable', 'string', 'max:1000'],
            'observation' => ['nullable', 'string', 'max:500'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $loan->update([
            'review' => $validated['review'] ?? null,
            'observation' => $validated['observation'] ?? null,
            'rating' => $validated['rating'] ?? null,
        ]);

        $loanService->checkIn($loan);

        return redirect()->route('dashboard')->with('success', 'Préstamo devuelto correctamente.');
    }
}
