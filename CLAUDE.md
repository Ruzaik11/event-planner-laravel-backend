# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

AI Event Planner Backend — a Laravel 13 REST API that generates personalized event plans using pluggable AI providers (OpenAI, Claude, Gemini). The API suggests venues, caterers, menus, budgets, and timelines based on user input, and integrates with Unsplash for event imagery.

## Commands

```bash
# Initial setup (install deps, generate .env, app key, migrate, npm install)
composer setup

# Development (Laravel server + queue + logs + Vite, all concurrently)
composer dev

# Run tests
composer test

# Individual commands
php artisan serve
php artisan migrate
php artisan db:seed
npm run dev
npm run build

# Code style (Laravel Pint)
./vendor/bin/pint
```

## Architecture

### Request Flow

```
HTTP Request
  → Form Request validation (app/Http/Requests/)
  → Controller (app/Http/Controllers/)
  → Service / Interface (app/Contracts/)
  → AI Generator (app/Services/AI/) or supporting service
  → JSON response
```

### Key Components

**Controllers**
- `EventPlanController` — generates event plans (`POST /api/event-plans`) and searches images (`GET /api/event-plans/image`)
- `EventMetaController` — returns form options for the frontend (`GET /api/event-plans/meta`)

**AI Generator Strategy Pattern** (`app/Services/AI/`)
- All generators implement `EventPlanGeneratorInterface` (`app/Contracts/`)
- Active provider is resolved at runtime from `config('ai.provider')` (set via `AI_PROVIDER` env var)
- `AIServiceProvider` binds the correct implementation to the interface
- `OpenAiEventPlanGenerator` and `ClaudeEventPlanGenerator` share identical prompt logic; `GeminiEventPlanGenerator` is a stub that throws

**Supporting Services**
- `RecaptchaService` — validates Google reCAPTCHA tokens (HTTP call to Google API, logs IP)
- `EventMetaService` — queries active cities, event types, and dietary preferences from DB
- `UnsplashImageSearch` — implements `ImageSearchInterface`, queries Unsplash API

**Validation**
- `GenerateEventPlanRequest` — validates event form fields, uses `exists:` rules against DB tables, and invokes the `ValidRecaptcha` custom rule
- `ValidRecaptcha` — custom validation rule that calls `RecaptchaService`

### Database

SQLite by default; configurable to MySQL/PostgreSQL via `.env`.

Tables: `cities`, `event_types`, `dietary_preferences` (all with `is_active` flag), plus standard Laravel tables (`users`, `cache`, `jobs`, `sessions`).

Models in `app/Models/` are basic Eloquent models with no custom relationships.

### API Endpoints

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/api/event-plans` | Generate an event plan |
| `GET` | `/api/event-plans/image` | Search Unsplash images (`?imageQuery=`) |
| `GET` | `/api/event-plans/meta` | Fetch cities, event types, dietary preferences |

### Environment Variables

| Variable | Purpose |
|----------|---------|
| `AI_PROVIDER` | `openai` \| `claude` \| `gemini` |
| `OPENAI_API_KEY` | OpenAI credentials |
| `ANTHROPIC_API_KEY` | Claude API key |
| `UNSPLASH_ACCESS_KEY` / `UNSPLASH_SECRET_KEY` | Image search |
| `RECAPTCHA_SECRET_KEY` | reCAPTCHA server-side validation |

### AI Prompt Engineering

Both OpenAI and Claude generators use a detailed system prompt that enforces:
- Real venue/caterer names (no invented names)
- Cultural and dietary compliance
- Strict budget adherence
- A specific JSON schema for the response

AI responses are stripped of markdown fences (` ```json `) before JSON parsing. Errors from invalid JSON are returned as structured HTTP responses.

### CORS

All origins are allowed on API routes (`/api/*`). Configured in `config/cors.php`.
