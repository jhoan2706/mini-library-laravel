<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use App\Models\Loan;

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

    public function checkin(User $user, Loan $loan): bool
    {
        return $user->hasRole(['admin', 'librarian']) || $loan->user_id === $user->id;
    }
}