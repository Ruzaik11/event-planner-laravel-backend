<?php
namespace App\Services;

use App\Models\City;
use App\Models\DietaryPreference;
use App\Models\EventType;

class EventMetaService
{
    public function getFormOptions(): array
    {

        $city = City::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $eventTypes = EventType::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $dietaryPreferences = DietaryPreference::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return [
            'cities'             => $city,
            'eventTypes'         => $eventTypes,
            'dietaryPreferences' => $dietaryPreferences,
        ];
    }
}
