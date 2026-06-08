<?php

namespace App\Domain\Scorm;

use App\Domain\User;
use Illuminate\Database\Eloquent\Model;

class SCORMTrack extends Model
{
    protected $table = 'scorm_tracks';

    protected $fillable = [
        'scorm_package_id',
        'user_id',
        'lesson_status',
        'completion_status',
        'progress',
        'score_raw',
        'score_min',
        'score_max',
        'session_time',
        'total_time',
        'suspend_data',
        'last_location',
        'runtime_data',
        'last_accessed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'decimal:2',
            'score_raw' => 'decimal:2',
            'score_min' => 'decimal:2',
            'score_max' => 'decimal:2',
            'runtime_data' => 'array',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function package()
    {
        return $this->belongsTo(SCORMPackage::class, 'scorm_package_id');
    }

    public function quizAttempt()
    {
        return $this->hasOne(SCORMQuizAttempt::class, 'scorm_track_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
