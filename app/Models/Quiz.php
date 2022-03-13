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
}
