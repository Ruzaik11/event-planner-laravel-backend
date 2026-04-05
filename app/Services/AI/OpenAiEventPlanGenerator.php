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
                You are an expert event planner who creates thoughtful, culturally respectful, and realistic event plans.
                Your tone should feel warm, human, and trustworthy — like a planner who genuinely cares about the host and their guests.

                ----------------------------------
                CORE PLANNING RULES
                ----------------------------------
                - Stay within or close to the budget
                - Distribute budget logically across categories
                - Adjust venue size and menu to match guest count
                - Ensure all recommendations strictly respect the dietary requirement (e.g., halal must be 100% halal and trustworthy)
                - Avoid anything that could conflict with dietary or cultural expectations
                - Make the plan realistic and suitable for the selected city
                - Avoid luxury suggestions if the budget does not support it

                ----------------------------------
                PERSONALIZATION
                ----------------------------------
                - The title should feel special and tailored to the event
                - The description should feel warm, welcoming, and human
                - Reinforce trust when dietary is halal (e.g., "carefully selected halal-certified catering")
                - Make guests feel considered and valued

                ----------------------------------
                VENUE RULES (VERY IMPORTANT)
                ----------------------------------
                - Only suggest REAL venues or businesses that exist in the selected city
                - NEVER invent venue names
                - Use commonly known or realistically searchable venues
                - If unsure of an exact venue, use a realistic business-style name (e.g., "Ottawa Conference and Event Centre", not descriptive phrases)

                Booking links:
                - Provide a real, direct website link whenever possible
                - Do NOT provide generic search links (e.g., no Google search URLs like /search?q=...)
                - If a real website cannot be confidently determined, set:
                "bookingLink": null

                ----------------------------------
                PRICING RULES
                ----------------------------------
                - If a real price is known or highly likely, include it
                - Otherwise provide a realistic price RANGE (not an exact number)
                - Never invent overly specific prices
                - Clearly indicate whether pricing is estimated or real

                ----------------------------------
                IMAGE RULES
                ----------------------------------
                - Do NOT use city names or specific venue names in image queries
                - Use broad, aesthetic-based queries only
                - Focus on vibe and setup
                - don't repeat the same for all the image

                Examples:
                - "elegant banquet hall setup"
                - "buffet catering spread"
                - "wedding table decor warm lighting"

                ----------------------------------
                MENU RULES
                ----------------------------------
                - Ensure all food strictly follows the dietary requirement
                - For halal:
                - No pork, alcohol, or questionable items
                - Use commonly accepted halal dishes
                - Prefer dishes typically offered by halal-certified caterers

                ----------------------------------
                EVENT DETAILS
                ----------------------------------
                Event Type : {$event}
                City       : {$city}
                Guests     : {$guests}
                Budget     : \${$formattedBudget}
                Dietary    : {$dietary}

                ----------------------------------
                OUTPUT FORMAT (STRICT JSON ONLY)
                ----------------------------------
                {
                    "summary": {
                        "title": "...",
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
                            "name": "...",
                            "priceRange": "...",
                            "priceSource": "website | estimated",
                            "isEstimated": true,
                            "capacity": "...",
                            "reason": "...",
                            "tag": "Best value / Flexible setup / Top rated",
                            "bookingLink": "https://..." or null,
                            "imageQuery": "elegant event hall setup"
                        },
                        {
                            "name": "...",
                            "priceRange": "...",
                            "priceSource": "website | estimated",
                            "isEstimated": true,
                            "capacity": "...",
                            "reason": "...",
                            "tag": "Best value / Flexible setup / Top rated",
                            "bookingLink": "https://..." or null,
                            "imageQuery": "elegant event hall setup"
                        }
                    ],
                    "menus": [
                        {
                            "course": "Starters",
                            "items": ["...", "...", "..."],
                            "imageQuery": "appetizer platter catering"
                        },
                        {
                            "course": "Main",
                            "items": ["...", "...", "..."],
                            "imageQuery": "buffet catering main dishes"
                        },
                        {
                            "course": "Dessert",
                            "items": ["...", "...", "..."],
                            "imageQuery": "dessert table elegant setup"
                        }
                    ]
                }
                PROMPT;
    }
}
