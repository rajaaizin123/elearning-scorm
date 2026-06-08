<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Academic\ClassGroup;
use App\Domain\Academic\Course;
use App\Domain\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassGroupController extends Controller
{
    public function index()
    {
        return view('admin.classes.index', ['classes' => ClassGroup::query()->with('course')->latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.classes.form', [
            'courses' => Course::query()->orderBy('title')->get(),
            'lecturers' => User::query()->where('role', 'dosen')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        ClassGroup::query()->create($this->validated($request));

        return redirect()->route('admin.classes.index')->with('status', 'Kelas berhasil dibuat.');
    }

    public function edit(ClassGroup $class)
    {
        return view('admin.classes.form', [
            'classGroup' => $class,
            'courses' => Course::query()->orderBy('title')->get(),
            'lecturers' => User::query()->where('role', 'dosen')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ClassGroup $class)
    {
        $class->update($this->validated($request));

        return redirect()->route('admin.classes.index')->with('status', 'Kelas berhasil diperbarui.');
    }

    public function destroy(ClassGroup $class)
    {
        $class->delete();

        return back()->with('status', 'Kelas berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'lecturer_id' => ['nullable', 'exists:users,id'],
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'string', 'max:50'],
        ]);
    }
}
