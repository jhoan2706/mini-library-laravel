<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Copy;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        // Ya no se crean usuarios de prueba
        // El primer usuario que se registre desde la UI será el admin

        Book::factory()
            ->count(15)
            ->has(Copy::factory()->count(2), 'copies')
            ->create();
    }
}