<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('books.update');
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'author' => ['sometimes', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'unique:books,isbn,'.$this->route('book')],
            'genre' => ['nullable', 'string', 'max:100'],
            'published_at' => ['nullable', 'integer', 'min:1000', 'max:'.date('Y')],
            'synopsis' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
