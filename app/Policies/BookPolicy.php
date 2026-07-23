<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function update(User $user, Book $book): bool
    {
        return $user->can('books.update');
    }

    public function delete(User $user, Book $book): bool
    {
        if (! $user->can('books.delete')) {
            return false;
        }

        // Regla de negocio a nivel de autorización: no se puede borrar un
        // libro con copias actualmente prestadas.
        return ! $book->copies()
            ->whereHas('loans', fn ($q) => $q->where('status', 'active'))
            ->exists();
    }
}
