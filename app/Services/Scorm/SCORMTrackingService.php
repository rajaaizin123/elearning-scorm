<?php

namespace App\Services\Scorm;

use App\Domain\Scorm\SCORMPackage;
use App\Domain\Scorm\SCORMQuizAttempt;
use App\Domain\Scorm\SCORMTrack;
use App\Domain\User;
use Illuminate\Support\Carbon;

class SCORMTrackingService
{
    public function __construct(private readonly SCORMRuntimeAPI $runtime)
    {
    }

    public function track(SCORMPackage $package, User $student, array $payload): SCORMTrack
    {
        $normalized = array_filter(
            $this->runtime->normalize($payload),
            fn ($value) => $value !== null
        );

        $lessonStatus = $normalized['lesson_status'] ?? 'incomplete';
        $progress = $this->progressFrom($normalized, $lessonStatus);

        $track = SCORMTrack::query()->updateOrCreate(
            [
                'scorm_package_id' => $package->id,
                'user_id' => $student->id,
            ],
            array_merge($normalized, [
                'lesson_status' => $lessonStatus,
                'completion_status' => $normalized['completion_status'] ?? $this->completionFrom($lessonStatus),
                'progress' => $progress,
                'last_accessed_at' => Carbon::now(),
            ])
        );

        $this->syncQuizAttempt($track, $normalized);

        return $track;
    }

    private function progressFrom(array $data, string $lessonStatus): float
    {
        if (isset($data['progress'])) {
            $progress = (float) $data['progress'];

            return $progress <= 1 ? round($progress * 100, 2) : min($progress, 100);
        }

        return in_array($lessonStatus, ['completed', 'passed'], true) ? 100 : 0;
    }

    private function completionFrom(string $lessonStatus): string
    {
        return match ($lessonStatus) {
            'completed', 'passed' => 'completed',
            'failed', 'incomplete' => 'incomplete',
            default => 'unknown',
        };
    }

    private function syncQuizAttempt(SCORMTrack $track, array $data): void
    {
        $lessonStatus = $data['lesson_status'] ?? $track->lesson_status;
        $runtimeData = $data['runtime_data'] ?? [];
        $hasQuizSignal = isset($data['score_raw'])
            || in_array($lessonStatus, ['passed', 'failed'], true)
            || $this->hasInteractions($runtimeData);

        if (! $hasQuizSignal) {
            return;
        }

        SCORMQuizAttempt::query()->updateOrCreate(
            ['scorm_track_id' => $track->id],
            [
                'scorm_package_id' => $track->scorm_package_id,
                'user_id' => $track->user_id,
                'answers' => $this->extractInteractions($runtimeData),
                'score' => $data['score_raw'] ?? $track->score_raw,
                'status' => $this->quizStatusFrom($lessonStatus),
                'submitted_at' => Carbon::now(),
            ]
        );
    }

    private function hasInteractions(array $runtimeData): bool
    {
        foreach (array_keys($runtimeData) as $key) {
            if (str_starts_with($key, 'cmi.interactions.')) {
                return true;
            }
        }

        return false;
    }

    private function extractInteractions(array $runtimeData): array
    {
        $interactions = [];

        foreach ($runtimeData as $key => $value) {
            if (preg_match('/^cmi\.interactions\.(\d+)\.(.+)$/', $key, $matches) !== 1) {
                continue;
            }

            $interactions[(int) $matches[1]][$matches[2]] = $value;
        }

        ksort($interactions);

        return array_values($interactions);
    }

    private function quizStatusFrom(string $lessonStatus): string
    {
        return match ($lessonStatus) {
            'passed' => 'passed',
            'failed' => 'failed',
            default => 'submitted',
        };
    }
}
