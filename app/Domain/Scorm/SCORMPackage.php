<?php

namespace App\Domain\Scorm;

use App\Domain\Academic\LearningModule;
use Illuminate\Database\Eloquent\Model;

class SCORMPackage extends Model
{
    protected $table = 'scorm_packages';

    protected $fillable = [
        'learning_module_id',
        'uuid',
        'title',
        'version',
        'zip_path',
        'extract_path',
        'launch_path',
        'manifest',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'manifest' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function module()
    {
        return $this->belongsTo(LearningModule::class, 'learning_module_id');
    }

    public function tracks()
    {
        return $this->hasMany(SCORMTrack::class, 'scorm_package_id');
    }

    public function quizAttempts()
    {
        return $this->hasMany(SCORMQuizAttempt::class, 'scorm_package_id');
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
