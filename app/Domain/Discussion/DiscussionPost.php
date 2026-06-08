<?php

namespace App\Domain\Discussion;

use App\Domain\User;
use Illuminate\Database\Eloquent\Model;

class DiscussionPost extends Model
{
    protected $fillable = ['discussion_id', 'user_id', 'body'];

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
