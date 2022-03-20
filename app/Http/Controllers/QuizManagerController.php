<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{

    public const NO_QUIZZES = "NO_QUIZZES";
    public const TOO_EARLY = "TOO_EARLY";
    public const PLAYING = "PLAYING";
    public const FINISHED = "FINISHED";
    public const TOO_LATE = "TOO_LATE";

    private $currentQuiz;

    public array $json = [];

    public function __construct()
    {
        $this->currentQuiz = Quiz::with('questions.choices')->notDone()->sortByOldestStartTime()->first();
    }

    public function getQuestion()
    {
        if (is_null($this->currentQuiz)) {
            $this->setJsonSuccess(false);
            $this->setJsonStatus(self::NO_QUIZZES);
            return response()->json($this->json);
        }

        $this->setJsonStartAt(strtotime($this->currentQuiz->start_at));
        $this->setJsonDuration($this->currentQuiz->duration);

        $this->presetResponseForQuizTimeContext();

        if ($this->getJsonStatus() !== self::PLAYING)
            return response()->json($this->json);
        /* ------------------------------------------------- */
        //      It's Quiz time
        /* ------------------------------------------------- */

        $answers = $this->currentQuiz->answers()->where('user_id', auth()->user()->id)->get();

        $questions_per_quiz = $this->currentQuiz->questions->count();

        if ($answers->count() === $questions_per_quiz) {
            if ((time() - strtotime($answers[$questions_per_quiz - 1]->served_at)) > $this->currentQuiz->questions->last()->duration
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

        $this->json['debug']['choice_number'] = $answers->last()?->choice_number;
        $this->json['debug']['received_at'] = $answers->last()?->received_at;

        if (
            $answers->count() > 0 &&
            empty($answers->last()->choice_number) && empty($answers->last()->received_at) &&
            (time() - strtotime($answers->last()->served_at)) <= $this->currentQuiz->questions[$answers->count() - 1]->duration
        ) {
            /* ------------------------------------------------- */
            //   didn't answer his latest question AND still can
            /* ------------------------------------------------- */

            $question = $this->currentQuiz->questions[$answers->count() - 1];
        } else {
            /* ------------------------------------------------- */
            // prepare new question + placeholder answer(served_at)
            /* ------------------------------------------------- */

            $question = $this->currentQuiz->questions[$answers->count()];

            Answer::create([
                'user_id' => auth()->user()->id,
                'question_id' => $question->id,
                'served_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->json['body']['question'] = $question;
        return response()->json($this->json);


        // DONT SERVE ENTIRE MODELS !
    }

    public function postAnswer(Request $request)
    {
        $received_at = time();

        $question = $this->currentQuiz->questions->filter(function ($question) use ($request) {
            return $question->id == $request->question_id;
        })->first();

        $answer = Answer::where('user_id', auth()->user()->id)
            ->where('question_id', $question->id)
            ->firstOrfail();

        if ($received_at - strtotime($answer->served_at) <= $question->duration) {
            $answer->choice_number = $request->choice_number;
            $answer->received_at = date('Y-m-d H:i:s', $received_at);
            $answer->save();
        }

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
