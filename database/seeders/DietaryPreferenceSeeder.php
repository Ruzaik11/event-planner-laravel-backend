<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DietaryPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $preferences = [
            'Halal',
            'Vegan',
            'Vegetarian',
            'Gluten-Free',
            'Dairy-Free',
            'Nut-Free',
            'Kosher',
            'Pescatarian',
            'Keto',
            'Low-Carb',
        ];

        foreach ($preferences as $pref) {
            DB::table('dietary_preferences')->updateOrInsert(
                ['slug' => Str::slug($pref)],
                [
                    'name' => $pref,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}