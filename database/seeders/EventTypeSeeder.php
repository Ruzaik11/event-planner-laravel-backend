<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Wedding',
            'Birthday Party',
            'Corporate Event',
            'Engagement Party',
            'Baby Shower',
            'Anniversary',
            'Graduation Party',
            'Conference',
            'Workshop',
            'Networking Event',
            'Festival',
            'Private Party',
        ];

        foreach ($types as $type) {
            DB::table('event_types')->updateOrInsert(
                ['slug' => Str::slug($type)],
                [
                    'name' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}