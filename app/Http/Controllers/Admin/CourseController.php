<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Academic\Course;
use App\Domain\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return view('admin.courses.index', ['courses' => Course::query()->with('lecturer')->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.courses.form', [
            'course' => null,
            'lecturers' => User::query()->where('role', 'dosen')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Course::query()->create($this->validated($request));

        return redirect()->route('admin.courses.index')->with('status', 'Course berhasil dibuat.');
    }

    public function edit(Course $course)
    {
        return view('admin.courses.form', [
            'course' => $course,
            'lecturers' => User::query()->where('role', 'dosen')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Course $course)
    {
        $course->update($this->validated($request));

        return redirect()->route('admin.courses.index')->with('status', 'Course berhasil diperbarui.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return back()->with('status', 'Course berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'semester' => ['required', 'string', 'max:100'],
            'lecturer_id' => ['nullable', 'exists:users,id'],
            'code' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'credit' => ['required', 'integer', 'min:1', 'max:6'],
            'status' => ['required', 'string', 'max:50'],
        ]);
    }
}
