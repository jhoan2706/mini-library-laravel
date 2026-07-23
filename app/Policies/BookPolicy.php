<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\Loan;
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

        return ! $book->copies()
            ->whereHas('loans', fn ($q) => $q->where('status', 'active'))
            ->exists();
    }

    // AGREGAR ESTE MÉTODO
    public function checkin(User $user, Loan $loan): bool
    {
        if ($user->hasRole(['admin', 'librarian'])) {
            return true;
        }

        return $user->id === $loan->user_id;
    }
}