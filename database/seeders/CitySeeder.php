<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            // Ontario
            ['Toronto', 'Ontario'],
            ['Ottawa', 'Ontario'],
            ['Mississauga', 'Ontario'],
            ['Brampton', 'Ontario'],
            ['Hamilton', 'Ontario'],
            ['London', 'Ontario'],
            ['Markham', 'Ontario'],
            ['Vaughan', 'Ontario'],
            ['Kitchener', 'Ontario'],
            ['Windsor', 'Ontario'],

            // British Columbia
            ['Vancouver', 'British Columbia'],
            ['Surrey', 'British Columbia'],
            ['Burnaby', 'British Columbia'],
            ['Richmond', 'British Columbia'],
            ['Kelowna', 'British Columbia'],
            ['Victoria', 'British Columbia'],

            // Alberta
            ['Calgary', 'Alberta'],
            ['Edmonton', 'Alberta'],
            ['Red Deer', 'Alberta'],
            ['Lethbridge', 'Alberta'],

            // Quebec
            ['Montreal', 'Quebec'],
            ['Quebec City', 'Quebec'],
            ['Laval', 'Quebec'],
            ['Gatineau', 'Quebec'],
            ['Longueuil', 'Quebec'],

            // Manitoba
            ['Winnipeg', 'Manitoba'],

            // Saskatchewan
            ['Saskatoon', 'Saskatchewan'],
            ['Regina', 'Saskatchewan'],

            // Nova Scotia
            ['Halifax', 'Nova Scotia'],

            // New Brunswick
            ['Moncton', 'New Brunswick'],
            ['Saint John', 'New Brunswick'],
            ['Fredericton', 'New Brunswick'],

            // Newfoundland and Labrador
            ["St. John's", 'Newfoundland and Labrador'],

            // PEI
            ['Charlottetown', 'Prince Edward Island'],

            // Territories
            ['Whitehorse', 'Yukon'],
            ['Yellowknife', 'Northwest Territories'],
            ['Iqaluit', 'Nunavut'],
        ];

        foreach ($cities as [$name, $province]) {
            DB::table('cities')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}