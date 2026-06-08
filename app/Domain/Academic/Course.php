<?php

namespace App\Domain\Academic;

use App\Domain\Discussion\Discussion;
use App\Domain\User;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'semester',
        'lecturer_id',
        'code',
        'title',
        'description',
        'credit',
        'status',
    ];

    public function lecturer()
    {
        return $this->belongsTo(User::class, 'lecturer_id');
    }

    public function classes()
    {
        return $this->hasMany(ClassGroup::class);
    }

    public function modules()
    {
        return $this->hasMany(LearningModule::class)
            ->orderBy('sort_order')
            ->orderBy('created_at');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }
}
