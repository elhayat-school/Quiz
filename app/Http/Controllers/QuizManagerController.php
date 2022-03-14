<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizManagerController extends Controller
{

    public const TOO_EARLY = "TOO_EARLY";
    public const PLAYING = "PLAYING";
    public const TOO_LATE = "TOO_LATE";

    public function getQuestion()
    {
        $quiz = Quiz::with('questions.choices')->currentQuiz();

        $response = $this->presetResponseForQuizTimeContext(strtotime($quiz->start_at), $quiz->duration);

        if ($response['status'] !== self::PLAYING)
            return response()->json($response);

        // NOT AS SIMPLE AS THIS
        $response['body']['question'] = $quiz->questions->first();

        return response()->json($response);
    }

    /**
     * ...++++++++++(start_at)---(start_at + duration)----------...
     *
     * @param int $start_at Quiz starting timestamp in seconds
     * @param int $duration Quiz duration in seconds
     */
    private function presetResponseForQuizTimeContext(int $start_at, int $duration): array
    {
        $time_diff = $start_at - date(time()); // ! Timezone

        if ($time_diff > 0)
            return ['success' => false, 'status' => self::TOO_EARLY, 'body' => ['t0' => $start_at]]; // * append T0

        elseif ($time_diff >= -$duration && $time_diff <= 0)
            return ['success' => true, 'status' => self::PLAYING]; // * append a question

        elseif ($time_diff < -$duration)
            return ['success' => false, 'status' => self::TOO_LATE, 'body' => ['t0' => $start_at]]; // * append ...
    }
}
