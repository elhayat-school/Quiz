<?php

function quiz_duration(int $questions_count): int
{
    return config('quiz.QUESTION_DEFAULT_DURATION') * $questions_count
        + config('quiz.QUIZ_MAX_DELAY')
        + config('quiz.QUIZ_EXTRA_TIME');
}
