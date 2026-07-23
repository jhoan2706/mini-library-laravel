<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CopyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'book_id' => Book::factory(),
            'barcode' => fake()->unique()->ean13(),
            'condition' => fake()->randomElement(['good', 'worn', 'damaged']),
        ];
    }
}
