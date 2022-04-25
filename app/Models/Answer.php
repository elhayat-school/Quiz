<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /* ------------------------------------------------- */
    //      SCOPES
    /* ------------------------------------------------- */

    public function scopeGetRanking($query, Collection $correct_choices)
    {
        $query->with('user')
            ->select([
                'user_id',
                DB::raw('SUM(UNIX_TIMESTAMP(received_at) - UNIX_TIMESTAMP(served_at)) AS sum_elapsed_seconds'),
                DB::raw('COUNT(DISTINCT question_id) AS count_correct_answers')
            ])
            ->filterCorrectChoices($correct_choices)
            ->orderBy('count_correct_answers', 'DESC')
            ->orderBy('sum_elapsed_seconds')
            ->groupBy('user_id');
    }

    /**
     * ! whereIn can't replace this scope
     *
     */
    public function scopeFilterCorrectChoices($query, Collection $correct_choices)
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
    }

    public function scopeCalculateEstablishmentParticipation($query, int|string $quiz_id, string $establishment)
    {
        $query
            ->join('users', 'users.id', '=', 'user_id')
            ->join('questions', 'questions.id', '=', 'question_id')
            ->select(DB::raw('COUNT(DISTINCT(answers.user_id)) as establishment_player_count'))
            ->where('questions.quiz_id', $quiz_id)
            ->where('users.establishment', $establishment);
    }
    public static function getEstablishmentParticipation(int|string $quiz_id, string $establishment)
    {
        return self::calculateEstablishmentParticipation($quiz_id, $establishment)
            ->pluck('establishment_player_count')
            ->first();
    }
}
