<?php

namespace App\Http\Controllers\Api;

use App\Domain\Scorm\SCORMPackage;
use App\Http\Controllers\Controller;
use App\Services\Scorm\SCORMTrackingService;
use Illuminate\Http\Request;

class SCORMTrackingController extends Controller
{
    public function store(Request $request, SCORMPackage $package, SCORMTrackingService $tracking)
    {
        $data = $request->validate([
            'runtime_data' => ['sometimes', 'array'],
            'progress' => ['sometimes', 'numeric', 'min:0', 'max:100'],
            'lesson_status' => ['sometimes', 'string', 'max:50'],
        ]);

        $track = $tracking->track($package, $request->user(), $data);

        return response()->json([
            'ok' => true,
            'track_id' => $track->id,
            'progress' => $track->progress,
            'lesson_status' => $track->lesson_status,
        ]);
    }
}
