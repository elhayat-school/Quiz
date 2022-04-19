<?php

namespace App\Services;

use App\Models\Choice;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class FullQuizInsertion
{

    public function insert($data)
    {
        DB::transaction(function () use ($data) {
            $quiz = Quiz::create([
                'start_at' => $data['start_at'],
                'duration' => quiz_duration(count($data['questions']))
            ]);

            foreach ($data['questions'] as $i => $question) {

                $inserted_question = $quiz->questions()->create([
                    'content' => $question['content'],
                    'duration' => config('quiz.QUESTION_DEFAULT_DURATION')
                ]);

                $data['questions'][$i]['id'] = $inserted_question->id;
            }

            $this->insertChoices($data['questions']);
        });
    }

    private function insertChoices($questions)
    {
        $choices = [];
        foreach ($questions as $question_data) {

            foreach ($question_data['choices'] as $j => $choice_content) {
                $choices[] = [
                    'question_id' => $question_data['id'],
                    'content' => $choice_content,
                    'choice_number' => "$j",
                    'is_correct' => "$j" === $question_data['is_correct'],
                    'created_at' => date('Y-m-d H:i:s'),
                ];
            }
        }
        Choice::insert($choices);
    }
}
