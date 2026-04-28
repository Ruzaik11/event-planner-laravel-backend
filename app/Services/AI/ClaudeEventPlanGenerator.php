<?php

namespace App\Services\AI;

use Anthropic\Client;
use App\Contracts\EventPlanGeneratorInterface;
use RuntimeException;

class ClaudeEventPlanGenerator implements EventPlanGeneratorInterface
{
    public function generate(array $eventData): array
    {
        $client = new Client(apiKey: config('ai.claude.api_key'));

        $response = $client->messages()->create(
            model: config('ai.claude.model'),
            maxTokens: config('ai.claude.max_tokens'),
            system: 'You are an expert event planner. Generate a realistic event plan based on budget and location. Always respond with valid JSON only.',
            messages: [
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
        );

        $rawContent  = $response->content[0]->text;
        $jsonContent = preg_replace('/```json|```/', '', $rawContent);
        $jsonContent = trim($jsonContent);

        $eventPlan = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($eventPlan)) {
            throw new RuntimeException('Claude returned invalid JSON: ' . json_last_error_msg());
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
                - If unsure of an exact venue, use a realistic business-style name (e.g., "Ottawa Conference and Event Centre")

                Booking links:
                - Provide a real, direct website link whenever possible
                - Do NOT provide generic search links (e.g., no Google search URLs)
                - If a real website cannot be confidently determined, set:
                "bookingLink": null

                ----------------------------------
                CATERER RULES (VERY IMPORTANT)
                ----------------------------------
                - Suggest 2 to 3 realistic catering providers suitable for the event
                - Caterers must match the dietary requirement strictly (e.g., halal-certified or clearly halal-friendly)
                - Caterers must be appropriate for the selected city
                - Do NOT invent unrealistic or random names
                - Only give catering which is a real place in the given city

                - Each caterer must:
                - Match the proposed menu style
                - Fit within the catering budget
                - Be suitable for the guest count

                Pricing:
                - Provide a realistic price range per person (e.g., "$15–$20 per person")
                - Do not give overly specific pricing unless confident
                - Keep pricing consistent with the budget breakdown

                Descriptions:
                - Keep explanation short, practical, and decision-focused
                - Explain WHY this caterer fits (budget, menu, reliability, etc.)

                Tags:
                - Use exactly ONE short tag per caterer:
                - "Best match"
                - "Popular choice"
                - "Budget friendly"

                Links:
                - If a real website is known, include it
                - Otherwise set "link": null

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
                - Do not repeat the same image query for all items

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
                - Ensure caterers can realistically provide the suggested menu items

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
                    ],
                    "caterers": [
                        {
                            "name": "...",
                            "pricePerPerson": "$15–$20",
                            "isEstimated": true,
                            "location": "...",
                            "dietarySupport": "...",
                            "description": "...",
                            "tag": "Best match / Popular choice / Budget friendly",
                            "link": "https://..." or null
                        }
                    ]
                }
                PROMPT;
    }
}