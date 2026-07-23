<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'author', 'isbn', 'genre',
        'published_at', 'synopsis', 'cover_url', 'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function copies()
    {
        return $this->hasMany(Copy::class);
    }

    // Scope reutilizable: solo libros con al menos una copia disponible ahora mismo
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('copies', function (Builder $q) {
            $q->whereDoesntHave('loans', fn (Builder $l) => $l->where('status', 'active'));
        });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->whereFullText(['title', 'author', 'synopsis'], $term)
            ->orWhere('genre', 'like', "%{$term}%")
            ->orWhere('isbn', $term);
    }

    public function availableCopiesCount(): int
    {
        return $this->copies()
            ->whereDoesntHave('loans', fn ($q) => $q->where('status', 'active'))
            ->count();
    }
}