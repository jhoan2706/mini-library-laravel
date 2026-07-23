<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookWebController extends Controller
{
    public function index()
    {
        $books = Book::query()
            ->with('copies')
            ->latest()
            ->get();

        return view('welcome', compact('books'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'copies_count' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $book = Book::create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'genre' => $validated['genre'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'synopsis' => $validated['synopsis'] ?? null,
        ]);

        $copies = [];
        for ($i = 0; $i < ($validated['copies_count'] ?? 1); $i++) {
            $copies[] = [
                'id' => (string) Str::uuid(),
                'barcode' => 'LIB' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                'condition' => 'good',
            ];
        }

        $book->copies()->createMany($copies);

        return redirect('/')->with('success', 'Book added successfully.');
    }
}
