<?php

namespace App\Domain\Discussion;

use App\Domain\Academic\Course;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = ['course_id', 'title', 'body', 'is_pinned', 'is_locked'];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function posts()
    {
        return $this->hasMany(DiscussionPost::class);
    }
}
