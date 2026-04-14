<?php
namespace App\Http\Controllers;

use App\Contracts\EventPlanGeneratorInterface;
use App\Contracts\ImageSearchInterface;
use App\Http\Requests\GenerateEventPlanRequest;
use App\Http\Requests\ImageSearchRequest;
use App\Services\RecaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventPlanController extends Controller
{
    public function __construct(
        private readonly EventPlanGeneratorInterface $eventPlanGenerator,
        private readonly ImageSearchInterface $imageSearch,
    ) {}

    public function generate(GenerateEventPlanRequest $request): JsonResponse
    {
        $eventPlan = $this->eventPlanGenerator->generate($request->safe()->except('recaptchaToken'));

        return response()->json($eventPlan);
    }

    public function searchImage(ImageSearchRequest $request)
    {
        $imageUrls = $this->imageSearch->search($request->imageQuery);

        return response()->json($imageUrls);
    }
}
