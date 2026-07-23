<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        $copies = [];
        for ($i = 0; $i < $request->integer('copies_count', 1); $i++) {
            $copies[] = [
                'id' => (string) Str::uuid(),
                'barcode' => 'LIB'.str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                'condition' => 'good',
            ];
        }

        $book->copies()->createMany($copies);

        return (new BookResource($book->fresh('copies')))
            ->response()
            ->setStatusCode(201);
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

    public function generateMetadata(Book $book, BookAiService $ai)
    {
        $this->authorize('update', $book);

        $result = $ai->inferMetadata($book);

        return response()->json(['metadata' => $result]);
    }
}
