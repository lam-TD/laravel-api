<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Product::create([
            'name' => 'Laravel',
            'logo' => 'https://laravel.com/img/logomark.min.svg',
            'status' => 1,
            'logo_color' => '#FF2D20',
            'description' => 'The PHP Framework For Web Artisans',
        ]);
    }
}
