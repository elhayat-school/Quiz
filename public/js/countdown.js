const quizCountdown = document.querySelector("#quiz-countdown");

if (quizCountdown) {
    let quizDelay = parseInt(quizCountdown.dataset.duration);
    const quizDelayFormat = quizCountdown.dataset.durationFormat;

    quizCountdown.innerHTML = moment
        .utc(quizDelay * 1000)
        .format(quizDelayFormat);

    setInterval(() => {
        quizDelay--;

        if (quizDelay <= 0) {
            location.reload();
            return;
        }

        quizDelay = quizDelay < 0 ? 0 : quizDelay;

        quizCountdown.innerHTML = moment
            .utc(quizDelay * 1000)
            .format("HH:mm:ss");
    }, 1000);
}

/* ------------------------------------------------- */
//      Question countdown
/* ------------------------------------------------- */
const questionCountdown = document.querySelector("#question-countdown");

if (questionCountdown) {
    let questionDuration = parseInt(questionCountdown.dataset.duration);
    const questionDurationFormat = questionCountdown.dataset.durationFormat;

    questionCountdown.innerHTML = moment
        .utc(questionDuration * 1000)
        .format(questionDurationFormat);

    setInterval(() => {
        questionDuration--;

        if (questionDuration <= 0) {
            location.reload();
            return;
        }

        questionDuration = questionDuration < 0 ? 0 : questionDuration;

        questionCountdown.innerHTML = moment
            .utc(questionDuration * 1000)
            .format(questionDurationFormat);
    }, 1000);
}
