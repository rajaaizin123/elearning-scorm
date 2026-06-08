<?php

namespace App\Http\Controllers;

use App\Domain\Academic\Course;
use App\Domain\Academic\Enrollment;
use App\Domain\Assessment\Assignment;
use App\Domain\Assessment\QuizAttempt;
use App\Domain\Discussion\Discussion;
use App\Domain\Scorm\SCORMPackage;
use App\Domain\Scorm\SCORMQuizAttempt;
use App\Domain\Scorm\SCORMTrack;
use App\Domain\User;
use App\Repositories\CourseRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly CourseRepository $courses)
    {
    }

    public function admin()
    {
        return view('dashboards.admin', [
            'stats' => [
                'users' => User::query()->count(),
                'courses' => Course::query()->count(),
                'active_courses' => Course::query()->where('status', 'published')->count(),
                'scorm_packages' => SCORMPackage::query()->count(),
            ],
        ]);
    }

    public function dosen(Request $request)
    {
        return view('dashboards.dosen', [
            'courses' => $this->courses->forLecturer($request->user()),
        ]);
    }

    public function mahasiswa(Request $request)
    {
        $user = $request->user();

        return view('dashboards.mahasiswa', [
            'courses' => $this->courses->activeForStudent($user),
            'progress' => SCORMTrack::query()->where('user_id', $user->id)->latest('last_accessed_at')->limit(6)->get(),
            'enrollmentCount' => Enrollment::query()->where('user_id', $user->id)->count(),
            'assignments' => Assignment::query()
                ->where('is_published', true)
                ->whereHas('course.classes.enrollments', fn ($query) => $query->where('user_id', $user->id))
                ->count(),
            'quizAttempts' => QuizAttempt::query()->where('user_id', $user->id)->count()
                + SCORMQuizAttempt::query()->where('user_id', $user->id)->count(),
            'discussions' => Discussion::query()
                ->whereHas('course.classes.enrollments', fn ($query) => $query->where('user_id', $user->id))
                ->count(),
        ]);
    }
}
