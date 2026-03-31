<?php
namespace App\Services\AI;

use App\Contracts\EventPlanGeneratorInterface;
use OpenAI\Laravel\Facades\OpenAI;
use RuntimeException;

class OpenAiEventPlanGenerator implements EventPlanGeneratorInterface
{
    public function generate(array $eventData): array
    {
        $apiResponse = OpenAI::chat()->create([
            'model'    => 'gpt-4o',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are an expert event planner. Generate a realistic event plan based on budget and location. Always respond with valid JSON only.',
                ],
                [
                    'role'    => 'user',
                    'content' => $this->buildPrompt(
                        $eventData['eventType'],
                        $eventData['city'],
                        $eventData['guestCount'],
                        $eventData['budget'],
                        $eventData['dietary']
                    ),
                ],
            ],
        ]);

        $rawContent  = $apiResponse->choices[0]->message->content;
        $jsonContent = preg_replace('/```json|```/', '', $rawContent);
        $jsonContent = trim($jsonContent);

        $eventPlan = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($eventPlan)) {
            throw new RuntimeException('OpenAI returned invalid JSON: ' . json_last_error_msg());
        }

        return $eventPlan;
    }

    private function buildPrompt(
        string $event,
        string $city,
        int $guests,
        float $budget,
        string $dietary
    ): string {

        $formattedBudget = number_format($budget, 2);

        return <<<PROMPT
                You are an expert event planner. Based on the details below, return a structured event plan.
                Event Type : {$event}
                City       : {$city}
                Guests     : {$guests}
                Budget     : \${$formattedBudget}
                Dietary    : {$dietary}
                Respond in this exact JSON format with no extra text:
                {
                    "summary": {
                        "title"      : "...",
                        "description": "..."
                    },
                    "budgetBreakdown": [
                        { "category": "Venue",         "amount": 0 },
                        { "category": "Catering",      "amount": 0 },
                        { "category": "Decor",         "amount": 0 },
                        { "category": "Entertainment", "amount": 0 },
                        { "category": "Contingency",   "amount": 0 }
                    ],
                    "timeline": [
                        { "label": "8 weeks before", "task": "..." },
                        { "label": "6 weeks before", "task": "..." },
                        { "label": "4 weeks before", "task": "..." }
                    ],
                    "venues": [
                        {
                            "name"       : "...",
                            "priceRange" : "...",
                            "capacity"   : "...",
                            "reason"     : "...",
                            "tag"        : "e.g. Best value / Flexible setup / Top rated",
                            "imageQuery" : "search query for unsplash for suitable image"
                        }
                    ],
                    "menus": [
                        {
                            "course"    : "Starters",
                            "items"     : ["...", "...", "..."],
                            "imageQuery": "search query for unsplash for suitable image"
                        },
                        {
                            "course"    : "Main",
                            "items"     : ["...", "...", "..."],
                            "imageQuery": "search query for unsplash for suitable image"
                        },
                        {
                            "course"    : "Dessert",
                            "items"     : ["...", "...", "..."],
                            "imageQuery": "search query for unsplash for suitable image"
                        }
                    ]
                }
                PROMPT;
    }
}
