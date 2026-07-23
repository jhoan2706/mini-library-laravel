<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'title' => fake()->sentence(3),
            'author' => fake()->name(),
            'isbn' => fake()->unique()->isbn13(),
            'genre' => fake()->randomElement(['Ciencia ficción', 'Fantasía', 'Realismo mágico', 'No ficción', 'Historia']),
            'published_at' => fake()->numberBetween(1950, 2023),
            'synopsis' => fake()->paragraph(),
            'tags' => fake()->randomElements(['clásico', 'best-seller', 'novedad', 'recomendado'], random_int(0, 3)),
        ];
    }
}
