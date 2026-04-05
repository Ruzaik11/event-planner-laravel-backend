<?php
namespace App\Http\Controllers;

use App\Services\EventMetaService;
use Illuminate\Http\JsonResponse;

class EventMetaController extends Controller
{
    public function __construct(EventMetaService $eventMetaService)
    {
        $this->eventMetaService = $eventMetaService;
    }

    public function index(): JsonResponse
    {
        return response()->json($this->eventMetaService->getFormOptions());
    }
}
