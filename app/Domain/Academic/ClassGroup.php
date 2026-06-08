<?php

namespace App\Domain\Academic;

use Illuminate\Database\Eloquent\Model;

class ClassGroup extends Model
{
    protected $fillable = ['course_id', 'lecturer_id', 'name', 'capacity', 'status'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
