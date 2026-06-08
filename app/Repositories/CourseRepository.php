<?php

namespace App\Repositories;

use App\Domain\Academic\Course;
use App\Domain\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseRepository
{
    public function activeForStudent(User $student): LengthAwarePaginator
    {
        return Course::query()
            ->where('status', 'published')
            ->whereHas('classes.enrollments', fn ($query) => $query->where('user_id', $student->id))
            ->with(['lecturer', 'modules.scormPackage'])
            ->latest()
            ->paginate(12);
    }

    public function forLecturer(User $lecturer): LengthAwarePaginator
    {
        return Course::query()
            ->where('lecturer_id', $lecturer->id)
            ->with(['modules.scormPackage'])
            ->withCount(['classes', 'modules'])
            ->latest()
            ->paginate(12);
    }
}
