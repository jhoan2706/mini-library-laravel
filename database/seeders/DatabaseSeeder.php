<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Copy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::create([
            'id' => Str::uuid(),
            'name' => 'Admin Principal',
            'email' => 'admin@library.dev',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        $librarian = User::create([
            'id' => Str::uuid(),
            'name' => 'Bibliotecaria Jefe',
            'email' => 'librarian@library.dev',
            'password' => Hash::make('password'),
        ]);
        $librarian->assignRole('librarian');

        $member = User::create([
            'id' => Str::uuid(),
            'name' => 'Lector Frecuente',
            'email' => 'member@library.dev',
            'password' => Hash::make('password'),
        ]);
        $member->assignRole('member');

        Book::factory()
            ->count(15)
            ->has(Copy::factory()->count(2), 'copies')
            ->create();
    }
}
