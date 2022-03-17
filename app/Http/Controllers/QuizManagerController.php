<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{

    public const TOO_EARLY = "TOO_EARLY";
    public const PLAYING = "PLAYING";
    public const FINISHED = "FINISHED";
    public const TOO_LATE = "TOO_LATE";

    public array $json = [];

    public function getQuestion()
    {
        // cover the beggining when there is no quizzes
        $quiz = Quiz::with('questions.choices')->currentQuiz();

        $this->setJsonStartAt(strtotime($quiz->start_at));
        $this->setJsonDuration($quiz->duration);

        $this->presetResponseForQuizTimeContext();

        if ($this->getJsonStatus() !== self::PLAYING)
            return response()->json($this->json);
        /* ------------------------------------------------- */
        //      It's Quiz time
        /* ------------------------------------------------- */

        $answers = $quiz->answers()->where('user_id', auth()->user()->id)->get();

        $questions_per_quiz = $quiz->questions->count();

        $this->json['debug']['answers'] = $answers;
        $this->json['debug']['answers_count'] = $answers->count();

        // ! hardcoded conditions
        if ($answers->count() === $questions_per_quiz) {
            if ((time() - strtotime($answers[$questions_per_quiz - 1]->served_at)) > 30
                || (!empty($answers[$questions_per_quiz - 1]->choice_number) &&
                    !empty($answers[$questions_per_quiz - 1]->received_at)
                )
            ) {

                $this->setJsonStatus(self::FINISHED);
                return response()->json($this->json);
            }
        }
        /* ------------------------------------------------- */
        //      User answered everything
        /* ------------------------------------------------- */

        $question = NULL;
        $answer = NULL;

        $this->json['debug']['choice_number'] = $answers->last()?->choice_number;
        $this->json['debug']['received_at'] = $answers->last()?->received_at;

        if (
            $answers->count() > 0 &&
            empty($answers->last()->choice_number) && empty($answers->last()->received_at) &&
            (time() - strtotime($answers->last()->served_at)) <= 30
        ) {
            /* ------------------------------------------------- */
            //   didn't answer his latest question AND still can
            /* ------------------------------------------------- */

            $question = $quiz->questions[$answers->count() - 1];
        } else {
            /* ------------------------------------------------- */
            // prepare new question + placeholder answer(served_at)
            /* ------------------------------------------------- */

            $question = $quiz->questions[$answers->count()];

            Answer::create([
                'user_id' => auth()->user()->id,
                'question_id' => $question->id,
                'served_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->json['body']['question'] = $question;
        return response()->json($this->json);
    }

    public function postAnswer()
    {
        return $this->getQuestion();
    }

    /**
     * ...++++++++++(start_at)---(start_at + duration)----------...
     *
     * @param int $start_at Quiz starting timestamp in seconds
     * @param int $duration Quiz duration in seconds
     */
    private function presetResponseForQuizTimeContext(): void
    {
        $time_diff = $this->getJsonStartAt() - time(); // ! Timezone

        if ($time_diff > 0) {
            $this->setJsonSuccess(false);
            $this->setJsonStatus(self::TOO_EARLY);
        }
        //
        elseif ($time_diff >= -$this->getJsonDuration() && $time_diff <= 0) {
            $this->setJsonSuccess(true);
            $this->setJsonStatus(self::PLAYING);
        }
        //
        elseif ($time_diff < -$this->getJsonDuration()) {
            $this->setJsonSuccess(false);
            $this->setJsonStatus(self::TOO_LATE);
        }
    }


    /* ----------------------------------------- */
    //      SETTING & GETTING JSON
    /* ----------------------------------------- */
    private function setJsonSuccess(bool $success): void
    {
        $this->json['success'] = $success;
    }

    private function setJsonStatus(string $status): void
    {
        $this->json['status'] = $status;
    }

    private function setJsonStartAt(int $start_at): void
    {
        $this->json['body']['start_at'] = $start_at;
    }

    private function setJsonDuration(int $duration): void
    {
        $this->json['body']['duration'] = $duration;
    }

    private function getJsonSuccess(): bool
    {
        return $this->json['success'];
    }

    private function getJsonStatus(): string
    {
        return $this->json['status'];
    }

    private function getJsonStartAt(): int
    {
        return $this->json['body']['start_at'];
    }

    private function getJsonDuration(): int
    {
        return $this->json['body']['duration'];
    }
}
