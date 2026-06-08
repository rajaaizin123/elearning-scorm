<?php

namespace App\Domain\Scorm;

use App\Domain\User;
use Illuminate\Database\Eloquent\Model;

class SCORMQuizAttempt extends Model
{
    protected $table = 'scorm_quiz_attempts';

    protected $fillable = [
        'scorm_track_id',
        'scorm_package_id',
        'user_id',
        'answers',
        'score',
        'status',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'score' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }

    public function track()
    {
        return $this->belongsTo(SCORMTrack::class, 'scorm_track_id');
    }

    public function package()
    {
        return $this->belongsTo(SCORMPackage::class, 'scorm_package_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
