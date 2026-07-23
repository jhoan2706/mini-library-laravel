<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('books.create');
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'unique:books,isbn'],
            'genre' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'integer', 'min:1000', 'max:'.date('Y')],
            'synopsis' => ['nullable', 'string', 'max:2000'],
            'copies_count' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}
