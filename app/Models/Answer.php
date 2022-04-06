<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ------------------------------------------------- */
    //      SCOPES
    /* ------------------------------------------------- */
    public function scopeFilterCorrectChoices($query, \Illuminate\Database\Eloquent\Collection $correct_choices)
    {

        foreach ($correct_choices as $i => $correct_choice) {
            if ($i === 0) {
                $query->where('question_id', $correct_choice->question_id)->where("choice_number", $correct_choice->choice_number);
                continue;
            }

            $query->orWhere(function ($query) use ($correct_choice) {
                $query->Where('question_id', $correct_choice->question_id)->where("choice_number", $correct_choice->choice_number);
            });
        }

        return $query;
    }
}
