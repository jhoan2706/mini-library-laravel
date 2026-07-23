<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class);

it('muestra la vista principal del dashboard y permite agregar un libro', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('Mini Library')
        ->assertSee('Add a new book');

    $response = $this->post('/books', [
        'title' => 'Dune',
        'author' => 'Frank Herbert',
        'genre' => 'Sci-Fi',
        'copies_count' => 2,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('books', ['title' => 'Dune']);
});
