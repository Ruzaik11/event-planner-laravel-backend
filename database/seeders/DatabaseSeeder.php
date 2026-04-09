<?php

namespace Database\Seeders;


use Database\Seeders\CitySeeder;
use Database\Seeders\DietaryPreferenceSeeder;
use Database\Seeders\EventTypeSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EventTypeSeeder::class,
            DietaryPreferenceSeeder::class,
            CitySeeder::class,
        ]);
    }
}
