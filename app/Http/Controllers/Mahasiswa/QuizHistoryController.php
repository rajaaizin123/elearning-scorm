<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Domain\Assessment\QuizAttempt;
use App\Domain\Scorm\SCORMQuizAttempt;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuizHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $lmsAttempts = QuizAttempt::query()
            ->with('quiz.course')
            ->where('user_id', $user->id)
            ->get()
            ->map(fn (QuizAttempt $attempt) => [
                'type' => 'LMS',
                'title' => $attempt->quiz?->title ?? 'Kuis LMS',
                'course' => $attempt->quiz?->course?->title,
                'score' => $attempt->score,
                'status' => $attempt->status,
                'submitted_at' => $attempt->submitted_at,
                'questions_count' => is_array($attempt->answers) ? count($attempt->answers) : 0,
            ]);

        $scormAttempts = SCORMQuizAttempt::query()
            ->with('package.module.course')
            ->where('user_id', $user->id)
            ->get()
            ->map(fn (SCORMQuizAttempt $attempt) => [
                'type' => 'SCORM',
                'title' => $attempt->package?->title ?? 'Kuis SCORM',
                'course' => $attempt->package?->module?->course?->title,
                'score' => $attempt->score,
                'status' => $attempt->status,
                'submitted_at' => $attempt->submitted_at,
                'questions_count' => is_array($attempt->answers) ? count($attempt->answers) : 0,
            ]);

        $attempts = $lmsAttempts
            ->concat($scormAttempts)
            ->sortByDesc(fn (array $attempt) => $attempt['submitted_at'])
            ->values();

        return view('mahasiswa.quizzes.index', [
            'attempts' => $attempts,
        ]);
    }
}
