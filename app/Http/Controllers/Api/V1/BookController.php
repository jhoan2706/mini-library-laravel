<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::query()
            ->search($request->query('q'))
            ->when($request->boolean('available_only'), fn ($q) => $q->available())
            ->when($request->query('genre'), fn ($q, $genre) => $q->where('genre', $genre))
            ->with('copies.loans')
            ->paginate($request->integer('per_page', 15));

        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request)
    {
        $book = Book::create($request->validated());
        $book->copies()->createMany(
            array_fill(0, $request->integer('copies_count', 1), [])
        );

        return new BookResource($book->fresh('copies'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);
        $book->update($request->validated());

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $book->delete(); // soft delete

        return response()->noContent();
    }
}
