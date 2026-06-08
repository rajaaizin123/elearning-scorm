<?php

namespace App\Domain\Academic;

use App\Domain\Scorm\SCORMPackage;
use Illuminate\Database\Eloquent\Model;

class LearningModule extends Model
{
    protected $table = 'modules';

    protected $fillable = ['course_id', 'title', 'description', 'type', 'sort_order', 'is_published'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function scormPackage()
    {
        return $this->hasOne(SCORMPackage::class);
    }
}
