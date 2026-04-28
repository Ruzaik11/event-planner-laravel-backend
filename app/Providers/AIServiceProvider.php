<?php

namespace App\Providers;

use App\Contracts\EventPlanGeneratorInterface;
use App\Services\AI\ClaudeEventPlanGenerator;
use App\Services\AI\OpenAiEventPlanGenerator;
use Illuminate\Support\ServiceProvider;

class AIServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EventPlanGeneratorInterface::class, fn() =>
            match (config('ai.provider')) {
                'openai' => new OpenAiEventPlanGenerator(),
                'claude' => new ClaudeEventPlanGenerator(),
                'gemini' => throw new \InvalidArgumentException('Gemini provider not yet implemented.'),
                default  => throw new \InvalidArgumentException(
                    'Unsupported AI provider: ' . config('services.ai_provider')
                ),
            }
        );
    }
}