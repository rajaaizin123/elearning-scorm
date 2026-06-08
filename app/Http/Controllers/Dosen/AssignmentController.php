<?php

namespace App\Http\Controllers\Dosen;

use App\Domain\Academic\Course;
use App\Domain\Assessment\Assignment;
use App\Domain\Assessment\Submission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $courses = Course::query()
            ->where('lecturer_id', $user->id)
            ->orderBy('title')
            ->get();

        $assignments = Assignment::query()
            ->with(['course', 'submissions.student'])
            ->whereHas('course', fn ($query) => $query->where('lecturer_id', $user->id))
            ->latest()
            ->get();

        return view('dosen.assignments.index', [
            'courses' => $courses,
            'assignments' => $assignments,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline_at' => ['nullable', 'date'],
            'max_score' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $this->authorizeCourse($request, (int) $data['course_id']);

        Assignment::query()->create([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'deadline_at' => $data['deadline_at'] ?? null,
            'max_score' => $data['max_score'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('dosen.assignments.index')
            ->with('status', 'Assignment berhasil dibuat.');
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorizeAssignment($request, $assignment);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'deadline_at' => ['nullable', 'date'],
            'max_score' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $assignment->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'deadline_at' => $data['deadline_at'] ?? null,
            'max_score' => $data['max_score'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('dosen.assignments.index')
            ->with('status', 'Assignment berhasil diperbarui.');
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $this->authorizeAssignment($request, $assignment);
        $assignment->delete();

        return redirect()
            ->route('dosen.assignments.index')
            ->with('status', 'Assignment berhasil dihapus.');
    }

    public function submissions(Request $request, Assignment $assignment)
    {
        $this->authorizeAssignment($request, $assignment);

        $assignment->load(['course', 'submissions.student']);

        return view('dosen.assignments.submissions', [
            'assignment' => $assignment,
        ]);
    }

    public function grade(Request $request, Assignment $assignment, Submission $submission)
    {
        $this->authorizeAssignment($request, $assignment);
        abort_unless($submission->assignment_id === $assignment->id, 404);

        $data = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:'.$assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $submission->update([
            'score' => $data['score'],
            'feedback' => $data['feedback'] ?? null,
            'graded_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('dosen.assignments.submissions.index', $assignment)
            ->with('status', 'Nilai submission berhasil disimpan.');
    }

    private function authorizeCourse(Request $request, int $courseId): void
    {
        abort_unless(
            Course::query()->whereKey($courseId)->where('lecturer_id', $request->user()->id)->exists(),
            403
        );
    }

    private function authorizeAssignment(Request $request, Assignment $assignment): void
    {
        abort_unless(
            Assignment::query()
                ->whereKey($assignment->id)
                ->whereHas('course', fn ($query) => $query->where('lecturer_id', $request->user()->id))
                ->exists(),
            403
        );
    }
}
