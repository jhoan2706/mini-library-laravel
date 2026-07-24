<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'genre',
        'published_at',
        'synopsis',
        'cover_url',
        'cover_image',
        'tags',
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
            $q->whereDoesntHave('loans', fn(Builder $l) => $l->where('status', 'active'));
        });
    }

    public function scopeSearch($query, $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where('title', 'LIKE', "%{$term}%")
            ->orWhere('author', 'LIKE', "%{$term}%")
            ->orWhere('genre', 'LIKE', "%{$term}%");
    }

    public function availableCopiesCount(): int
    {
        return $this->copies()
            ->whereDoesntHave('loans', fn($q) => $q->where('status', 'active'))
            ->count();
    }

    public function getCoverImageUrl(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return 'https://picsum.photos/seed/' . $this->id . '/300/400';
    }
}
