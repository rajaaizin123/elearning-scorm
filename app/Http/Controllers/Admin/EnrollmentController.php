<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Academic\ClassGroup;
use App\Domain\Academic\Enrollment;
use App\Domain\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(ClassGroup $classGroup)
    {
        return view('admin.enrollments.index', [
            'classGroup' => $classGroup->load('course', 'enrollments.student'),
            'enrolledStudents' => $classGroup->enrollments()->with('student')->get(),
        ]);
    }

    public function create(ClassGroup $classGroup)
    {
        $enrolledIds = $classGroup->enrollments()->pluck('user_id')->toArray();

        return view('admin.enrollments.form', [
            'classGroup' => $classGroup->load('course'),
            'students' => User::query()
                ->where('role', 'mahasiswa')
                ->whereNotIn('id', $enrolledIds)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request, ClassGroup $classGroup)
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'exists:users,id'],
        ]);

        foreach ($validated['user_ids'] as $userId) {
            Enrollment::query()->firstOrCreate(
                ['class_group_id' => $classGroup->id, 'user_id' => $userId],
                ['status' => 'active', 'enrolled_at' => now()]
            );
        }

        return redirect()
            ->route('admin.classes.enrollments.index', $classGroup)
            ->with('status', 'Mahasiswa berhasil didaftarkan ke kelas.');
    }

    public function destroy(ClassGroup $classGroup, Enrollment $enrollment)
    {
        if ($enrollment->class_group_id !== $classGroup->id) {
            abort(403);
        }

        $enrollment->delete();

        return back()->with('status', 'Mahasiswa berhasil dihapus dari kelas.');
    }
}
