<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Domain\Assessment\Assignment;
use App\Domain\Assessment\Submission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $assignments = Assignment::query()
            ->with([
                'course',
                'submissions' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->where('is_published', true)
            ->whereHas('course.classes.enrollments', fn ($query) => $query->where('user_id', $user->id))
            ->latest('deadline_at')
            ->get();

        return view('mahasiswa.assignments.index', [
            'assignments' => $assignments,
        ]);
    }

    public function store(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        abort_unless($this->canAccess($assignment, $user->id), 403);

        $data = $request->validate([
            'file' => ['nullable', 'file', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (! $request->hasFile('file') && blank($data['notes'] ?? null)) {
            return back()
                ->withErrors(['submission' => 'Upload file atau isi catatan terlebih dahulu.'])
                ->withInput();
        }

        $submission = Submission::query()->firstOrNew([
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
        ]);

        if ($request->hasFile('file')) {
            if ($submission->file_path) {
                Storage::disk('public')->delete($submission->file_path);
            }

            $submission->file_path = $request->file('file')->store("submissions/{$assignment->id}/{$user->id}", 'public');
        }

        $submission->notes = $data['notes'] ?? null;
        $submission->submitted_at = Carbon::now();
        $submission->save();

        return redirect()
            ->route('mahasiswa.assignments.index')
            ->with('status', 'Submission berhasil disimpan.');
    }

    private function canAccess(Assignment $assignment, int $userId): bool
    {
        return Assignment::query()
            ->whereKey($assignment->id)
            ->where('is_published', true)
            ->whereHas('course.classes.enrollments', fn ($query) => $query->where('user_id', $userId))
            ->exists();
    }
}
