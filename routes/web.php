<?php

use App\Http\Controllers\Admin\ClassGroupController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dosen\AssignmentController;
use App\Http\Controllers\Dosen\LearningModuleController;
use App\Http\Controllers\Dosen\SCORMPackageController;
use App\Http\Controllers\Api\SCORMTrackingController;
use App\Http\Controllers\Mahasiswa\AssignmentSubmissionController;
use App\Http\Controllers\Mahasiswa\DiscussionController;
use App\Http\Controllers\Mahasiswa\QuizHistoryController;
use App\Http\Controllers\Mahasiswa\SCORMPlayerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    $user = request()->user();

    if ($user?->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->hasRole('dosen')) {
        return redirect()->route('dosen.dashboard');
    }

    if ($user?->hasRole('mahasiswa')) {
        return redirect()->route('mahasiswa.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::redirect('/', '/admin/dashboard');
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('courses', CourseController::class)->except(['show']);
        Route::resource('classes', ClassGroupController::class)->except(['show']);

        Route::get('/classes/{classGroup}/enrollments', [EnrollmentController::class, 'index'])->name('classes.enrollments.index');
        Route::get('/classes/{classGroup}/enrollments/create', [EnrollmentController::class, 'create'])->name('classes.enrollments.create');
        Route::post('/classes/{classGroup}/enrollments', [EnrollmentController::class, 'store'])->name('classes.enrollments.store');
        Route::delete('/classes/{classGroup}/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('classes.enrollments.destroy');
    });

    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::redirect('/', '/dosen/dashboard');
        Route::get('/dashboard', [DashboardController::class, 'dosen'])->name('dashboard');
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::post('/assignments', [AssignmentController::class, 'store'])->name('assignments.store');
        Route::put('/assignments/{assignment}', [AssignmentController::class, 'update'])->name('assignments.update');
        Route::delete('/assignments/{assignment}', [AssignmentController::class, 'destroy'])->name('assignments.destroy');
        Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions'])->name('assignments.submissions.index');
        Route::put('/assignments/{assignment}/submissions/{submission}/grade', [AssignmentController::class, 'grade'])->name('assignments.submissions.grade');
        Route::resource('modules', LearningModuleController::class)->except(['show']);
        Route::post('/scorm-packages', [SCORMPackageController::class, 'store'])->name('scorm-packages.store');
    });

    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::redirect('/', '/mahasiswa/dashboard');
        Route::get('/dashboard', [DashboardController::class, 'mahasiswa'])->name('dashboard');
        Route::get('/assignments', [AssignmentSubmissionController::class, 'index'])->name('assignments.index');
        Route::post('/assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'store'])->name('assignments.submissions.store');
        Route::get('/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
        Route::post('/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
        Route::get('/discussions/{discussion}', [DiscussionController::class, 'show'])->name('discussions.show');
        Route::post('/discussions/{discussion}/replies', [DiscussionController::class, 'reply'])->name('discussions.reply');
        Route::get('/quizzes', [QuizHistoryController::class, 'index'])->name('quizzes.index');
        Route::get('/scorm/{package:uuid}', [SCORMPlayerController::class, 'show'])->name('scorm.show');
        Route::post('/scorm/{package:uuid}/track', [SCORMTrackingController::class, 'store'])
            ->middleware('throttle:scorm')
            ->name('scorm.track');
    
    });

});

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
