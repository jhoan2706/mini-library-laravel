<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

uses(DatabaseTransactions::class, Tests\TestCase::class);

beforeEach(function () {
    // Crear roles
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'librarian', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

    // Crear TODOS los permisos necesarios
    $permissions = [
        'books.create', 'books.update', 'books.delete', 'books.view',
        'loans.checkout', 'loans.checkin', 'dashboard.view', 'users.manage'
    ];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    // Asignar permisos al rol librarian
    $librarian = Role::where('name', 'librarian')->first();
    $librarian->syncPermissions([
        'books.create', 'books.update', 'books.delete', 'books.view',
        'loans.checkout', 'loans.checkin', 'dashboard.view'
    ]);
});

it('un member no puede crear libros', function () {
    $member = User::factory()->create();
    $member->assignRole('member');

    $response = $this->actingAs($member)
        ->postJson('/api/v1/books', [
            'title' => 'X',
            'author' => 'Y',
            'copies_count' => 1
        ]);

    $response->assertForbidden();
});

it('un librarian sí puede crear libros', function () {
    $librarian = User::factory()->create();
    $librarian->assignRole('librarian');

    $response = $this->actingAs($librarian)
        ->postJson('/api/v1/books', [
            'title' => 'Dune',
            'author' => 'Frank Herbert',
            'copies_count' => 2,
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Dune')
        ->assertJsonPath('data.total_copies', 2);
});

it('la búsqueda por texto encuentra por título', function () {
    Book::factory()->create(['title' => 'Fundación', 'author' => 'Isaac Asimov', 'genre' => 'Ciencia ficción']);
    Book::factory()->create(['title' => 'El Aleph', 'author' => 'Jorge Luis Borges', 'genre' => 'Cuento']);

    $response = $this->actingAs(User::factory()->create())
        ->getJson('/api/v1/books?q=Asimov');

    $response->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Fundación');
});

it('no permite prestar dos veces la misma copia', function () {
    $copy = \App\Models\Copy::factory()->create();
    $user = User::factory()->create();
    $user->assignRole('member');

    app(\App\Services\LoanService::class)->checkOut($copy, $user);

    $response = $this->actingAs($user)
        ->postJson("/api/v1/copies/{$copy->id}/check-out");

    $response->assertStatus(409);
});