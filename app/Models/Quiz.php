<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function choices()
    {
        return $this->hasManyThrough(Choice::class, Question::class);
    }

    public function answers()
    {
        return $this->hasManyThrough(Answer::class, Question::class);
    }

    /* ------------------------------------------------- */
    //      SCOPES
    /* ------------------------------------------------- */
    public function scopeNotDone($query)
    {
        return $query->where('done', false);
    }

    public function scopeSortByOldestStartTime($query)
    {
        return $query->oldest('start_at');
    }
}
