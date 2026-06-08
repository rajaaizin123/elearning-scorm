<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Domain\Academic\Course;
use App\Domain\Discussion\Discussion;
use App\Domain\Discussion\DiscussionPost;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $courses = Course::query()
            ->where('status', 'published')
            ->whereHas('classes.enrollments', fn ($query) => $query->where('user_id', $user->id))
            ->orderBy('title')
            ->get();

        $discussions = Discussion::query()
            ->with(['course', 'posts.author'])
            ->withCount('posts')
            ->whereHas('course.classes.enrollments', fn ($query) => $query->where('user_id', $user->id))
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();

        return view('mahasiswa.discussions.index', [
            'courses' => $courses,
            'discussions' => $discussions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $this->authorizeCourse($request, (int) $data['course_id']);

        $discussion = Discussion::query()->create([
            'course_id' => $data['course_id'],
            'title' => $data['title'],
            'body' => $data['body'],
        ]);

        DiscussionPost::query()->create([
            'discussion_id' => $discussion->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('mahasiswa.discussions.show', $discussion)
            ->with('status', 'Diskusi berhasil dibuat.');
    }

    public function show(Request $request, Discussion $discussion)
    {
        $this->authorizeDiscussion($request, $discussion);

        $discussion->load(['course', 'posts.author']);

        return view('mahasiswa.discussions.show', [
            'discussion' => $discussion,
        ]);
    }

    public function reply(Request $request, Discussion $discussion)
    {
        $this->authorizeDiscussion($request, $discussion);
        abort_if($discussion->is_locked, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        DiscussionPost::query()->create([
            'discussion_id' => $discussion->id,
            'user_id' => $request->user()->id,
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('mahasiswa.discussions.show', $discussion)
            ->with('status', 'Balasan berhasil dikirim.');
    }

    private function authorizeCourse(Request $request, int $courseId): void
    {
        abort_unless(
            Course::query()
                ->whereKey($courseId)
                ->whereHas('classes.enrollments', fn ($query) => $query->where('user_id', $request->user()->id))
                ->exists(),
            403
        );
    }

    private function authorizeDiscussion(Request $request, Discussion $discussion): void
    {
        abort_unless(
            Discussion::query()
                ->whereKey($discussion->id)
                ->whereHas('course.classes.enrollments', fn ($query) => $query->where('user_id', $request->user()->id))
                ->exists(),
            403
        );
    }
}
