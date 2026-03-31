<?php

namespace App\Providers;

use App\Contracts\EventPlanGeneratorInterface;
use App\Services\AI\OpenAiEventPlanGenerator;
use App\Services\AI\ClaudeService;
use App\Services\AI\GeminiService;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EventPlanGeneratorInterface::class, fn() =>
            match (config('ai.provider')) {
                'openai' => new OpenAiEventPlanGenerator(),
                'claude' => new ClaudeService(),
                'gemini' => new GeminiService(),
                default  => throw new \InvalidArgumentException(
                    'Unsupported AI provider: ' . config('services.ai_provider')
                ),
            }
        );
    }
}