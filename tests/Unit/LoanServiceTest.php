<?php

use App\Exceptions\CopyNotAvailableException;
use App\Models\Copy;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class);

beforeEach(function () {
    // Crear roles y permisos solo para el test
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'librarian', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'member', 'guard_name' => 'web']);

    $permissions = ['loans.checkout', 'loans.checkin'];
    foreach ($permissions as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
});

it('permite hacer check-out de una copia disponible', function () {
    $copy = Copy::factory()->create();
    $user = User::factory()->create();

    $loan = app(LoanService::class)->checkOut($copy, $user);

    expect($loan->status)->toBe('active')
        ->and((string) $loan->copy_id)->toEqual((string) $copy->id)
        ->and($loan->due_date->diffInDays(now()))->toBeLessThanOrEqual(14);
});

it('rechaza el check-out si la copia ya está prestada', function () {
    $copy = Copy::factory()->create();
    $existingBorrower = User::factory()->create();
    $newBorrower = User::factory()->create();

    app(LoanService::class)->checkOut($copy, $existingBorrower);

    expect(fn () => app(LoanService::class)->checkOut($copy, $newBorrower))
        ->toThrow(CopyNotAvailableException::class);
});

it('marca el préstamo como devuelto al hacer check-in', function () {
    $copy = Copy::factory()->create();
    $user = User::factory()->create();
    $loan = app(LoanService::class)->checkOut($copy, $user);

    $returned = app(LoanService::class)->checkIn($loan);

    expect($returned->status)->toBe('returned')
        ->and($returned->checked_in_at)->not->toBeNull();
});
