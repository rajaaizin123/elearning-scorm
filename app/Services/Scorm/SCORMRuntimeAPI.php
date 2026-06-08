<?php

namespace App\Services\Scorm;

class SCORMRuntimeAPI
{
    public function normalize(array $payload): array
    {
        $data = $payload['runtime_data'] ?? $payload;

        return [
            'lesson_status' => $data['cmi.core.lesson_status'] ?? $data['cmi.success_status'] ?? $data['lesson_status'] ?? null,
            'completion_status' => $data['cmi.completion_status'] ?? $data['completion_status'] ?? null,
            'score_raw' => $data['cmi.core.score.raw'] ?? $data['cmi.score.raw'] ?? null,
            'score_min' => $data['cmi.core.score.min'] ?? $data['cmi.score.min'] ?? null,
            'score_max' => $data['cmi.core.score.max'] ?? $data['cmi.score.max'] ?? null,
            'session_time' => $data['cmi.core.session_time'] ?? $data['cmi.session_time'] ?? null,
            'total_time' => $data['cmi.core.total_time'] ?? $data['cmi.total_time'] ?? null,
            'suspend_data' => $data['cmi.suspend_data'] ?? null,
            'last_location' => $data['cmi.core.lesson_location'] ?? $data['cmi.location'] ?? null,
            'progress' => $data['cmi.progress_measure'] ?? $payload['progress'] ?? null,
            'runtime_data' => $data,
        ];
    }
}
