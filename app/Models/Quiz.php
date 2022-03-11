<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /* ************************************************* */
    //      SCOPES
    /* ************************************************* */
    public function scopeCurrentQuiz($query)
    {
        return $query->notDone()->sortSmallestOrder()->first();
    }

    public function scopeNotDone($query)
    {
        return $query->where('done', false);
    }

    public function scopeSortSmallestOrder($query)
    {
        return $query->oldest('order');
    }
}
