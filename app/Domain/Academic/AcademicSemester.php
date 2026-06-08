<?php

namespace App\Domain\Academic;

use Illuminate\Database\Eloquent\Model;

class AcademicSemester extends Model
{
    protected $fillable = ['name', 'code', 'starts_at', 'ends_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
