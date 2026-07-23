<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'genre' => $this->genre,
            'published_at' => $this->published_at,
            'synopsis' => $this->synopsis,
            'tags' => $this->tags,
            'total_copies' => $this->copies->count(),
            'available_copies' => $this->availableCopiesCount(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
