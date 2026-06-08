<?php

namespace App\Domain\Assessment;

use App\Domain\Academic\Course;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'deadline_at', 'max_score', 'is_published'];

    protected function casts(): array
    {
        return [
            'deadline_at' => 'datetime',
            'is_published' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
