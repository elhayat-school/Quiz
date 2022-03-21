const quizCountdown = document.querySelector("#quiz-countdown");

if (quizCountdown) {
    const quizCountdownHours = quizCountdown.querySelector(
        "#quiz-countdown-hours"
    );
    const quizCountdownMinutes = quizCountdown.querySelector(
        "#quiz-countdown-minutes"
    );
    const quizCountdownSeconds = quizCountdown.querySelector(
        "#quiz-countdown-seconds"
    );

    let quizDelay = parseInt(quizCountdown.dataset.quizDelay);
    let quizDelayDate = new Date(quizDelay * 1000);

    quizCountdownMinutes.innerHTML = quizDelayDate.getHours();
    quizCountdownMinutes.innerHTML = quizDelayDate.getMinutes();
    quizCountdownSeconds.innerHTML = quizDelayDate.getSeconds();

    setInterval(() => {
        quizDelay--;

        if (quizDelay <= 0) {
            location.reload();
            return;
        }

        quizDelay = quizDelay < 0 ? 0 : quizDelay;

        let quizDelayDate = new Date(quizDelay * 1000);

        quizCountdownHours.innerHTML = quizDelayDate.getHours();
        quizCountdownMinutes.innerHTML = quizDelayDate.getMinutes();
        quizCountdownSeconds.innerHTML = quizDelayDate.getSeconds();
    }, 1000);
}

/* ------------------------------------------------- */
//      Question countdown
/* ------------------------------------------------- */
const questionCountdown = document.querySelector("#question-countdown");

if (questionCountdown) {
    const questionCountdownMinutes = questionCountdown.querySelector(
        "#question-countdown-minutes"
    );
    const questionCountdownSeconds = questionCountdown.querySelector(
        "#question-countdown-seconds"
    );

    let questionDuration = parseInt(questionCountdown.dataset.questionDuration);
    let questionDurationDate = new Date(questionDuration * 1000);

    questionCountdownMinutes.innerHTML = questionDurationDate.getMinutes();
    questionCountdownSeconds.innerHTML = questionDurationDate.getSeconds();

    setInterval(() => {
        questionDuration--;

        if (questionDuration <= 0) {
            location.reload();
            return;
        }

        questionDuration = questionDuration < 0 ? 0 : questionDuration;

        let questionDurationDate = new Date(questionDuration * 1000);

        questionCountdownMinutes.innerHTML = questionDurationDate.getMinutes();
        questionCountdownSeconds.innerHTML = questionDurationDate.getSeconds();
    }, 1000);
}
