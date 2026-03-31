<?php
namespace App\Http\Controllers;

use App\Contracts\EventPlanGeneratorInterface;
use App\Contracts\ImageSearchInterface;
use App\Http\Requests\GenerateEventPlanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventPlanController extends Controller
{
    public function __construct(
        private readonly EventPlanGeneratorInterface $eventPlanGenerator,
        private readonly ImageSearchInterface $imageSearch
    ) {}

    public function generate(GenerateEventPlanRequest $request): JsonResponse
    {
        $eventPlan = $this->eventPlanGenerator->generate($request->validated());

        return response()->json($eventPlan);
    }

    public function searchImage(Request $request)
    {
        $imageUrls = $this->imageSearch->search($request->imageQuery);

        return response()->json($imageUrls);
    }
}
