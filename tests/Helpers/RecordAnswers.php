<?php

namespace Tests\Helpers;

use App\Models\Question;

class RecordAnswers
{
    public static function exec(int $last_answer_index = 0): void
    {

        // echo "================== $last_answer_index";

        $questions = Question::all();

        if ($last_answer_index > $questions->count())
            throw new \Exception("Cannot record $last_answer_index answers for the quiz", 1);

        for ($i = 0; $i <= $last_answer_index; $i++) {
            if ($i < $last_answer_index) {

                $ans = $questions[$i]->answers()->create([
                    'user_id' => auth()->user()->id,
                    'choice_number' => rand(1, 4),
                    // Introduce multiplication
                    'served_at' => date('Y-m-d H:i:s',  time() - config('quiz.QUESTION_DEFAULT_DURATION') - 2),
                    'received_at' => date('Y-m-d H:i:s',  time() - 5),
                ]);
                // dump($ans->toArray());
            } else {
                $ans = $questions[$i]->answers()->create([
                    'user_id' => auth()->user()->id,
                    // Introduce multiplication
                    'served_at' => date('Y-m-d H:i:s',  time() - 2),
                ]);
                // dump($ans->toArray());
            }
        }
    }
}
