<?php

namespace App\Http\Controllers\Dosen;

use App\Domain\Academic\Course;
use App\Domain\Academic\LearningModule;
use App\Http\Controllers\Controller;
use App\Http\Requests\LearningModuleRequest;
use Illuminate\Http\Request;

class LearningModuleController extends Controller
{
    public function index(Request $request)
    {
        $modules = LearningModule::query()
            ->whereHas('course', fn ($query) => $query->where('lecturer_id', $request->user()->id))
            ->with(['course', 'scormPackage'])
            ->latest()
            ->paginate(12);

        return view('dosen.modules.index', [
            'modules' => $modules,
        ]);
    }

    public function create(Request $request)
    {
        $courses = Course::query()
            ->where('lecturer_id', $request->user()->id)
            ->orderBy('title')
            ->get();

        return view('dosen.modules.form', [
            'module' => null,
            'courses' => $courses,
        ]);
    }

    public function store(LearningModuleRequest $request)
    {
        $course = Course::query()
            ->where('id', $request->input('course_id'))
            ->where('lecturer_id', $request->user()->id)
            ->firstOrFail();

        LearningModule::query()->create([
            'course_id' => $course->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'sort_order' => $request->input('sort_order', 0),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('dosen.modules.index')
            ->with('status', 'Modul berhasil dibuat.');
    }

    public function edit(Request $request, LearningModule $module)
    {
        $this->authorizeModule($request, $module);

        $courses = Course::query()
            ->where('lecturer_id', $request->user()->id)
            ->orderBy('title')
            ->get();

        return view('dosen.modules.form', [
            'module' => $module,
            'courses' => $courses,
        ]);
    }

    public function update(LearningModuleRequest $request, LearningModule $module)
    {
        $this->authorizeModule($request, $module);

        $course = Course::query()
            ->where('id', $request->input('course_id'))
            ->where('lecturer_id', $request->user()->id)
            ->firstOrFail();

        $module->update([
            'course_id' => $course->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'sort_order' => $request->input('sort_order', 0),
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('dosen.modules.index')
            ->with('status', 'Modul berhasil diperbarui.');
    }

    public function destroy(Request $request, LearningModule $module)
    {
        $this->authorizeModule($request, $module);

        $module->delete();

        return redirect()
            ->route('dosen.modules.index')
            ->with('status', 'Modul berhasil dihapus.');
    }

    private function authorizeModule(Request $request, LearningModule $module): void
    {
        abort_unless($module->course?->lecturer_id === $request->user()->id, 403);
    }
}
