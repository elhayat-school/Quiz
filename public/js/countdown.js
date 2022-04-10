document.querySelectorAll("[CountDown]").forEach((el) => {
    const TIMERFORMAT = el.dataset.countdownFormat;

    const COUNTDOWNSTEPSECONDS = (function () {
        const COUNTDOWNSTEP = el.dataset.countdownStep.match(
            /(?:(?<h>\d{0,2})h)?(?:(?<m>\d{0,2})m)?(?:(?<s>\d{0,2})s)?/
        );

        return (
            (COUNTDOWNSTEP.groups.h
                ? parseInt(COUNTDOWNSTEP.groups.h) * 3600
                : 0) +
            (COUNTDOWNSTEP.groups.m
                ? parseInt(COUNTDOWNSTEP.groups.m) * 60
                : 0) +
            (COUNTDOWNSTEP.groups.s ? parseInt(COUNTDOWNSTEP.groups.s) : 0)
        );
    })();

    const TARGETTIMESTAMP = setStartTimestamp(
        parseInt(el.dataset.countdownDuration)
    );

    let remainingMilliSeconds = TARGETTIMESTAMP - Date.now();

    // init
    el.innerHTML = moment.utc(remainingMilliSeconds).format(TIMERFORMAT);

    setInterval(() => {
        remainingMilliSeconds = TARGETTIMESTAMP - Date.now();

        if (remainingMilliSeconds <= 0) {
            location.reload();
            return;
        }

        remainingMilliSeconds =
            remainingMilliSeconds < 0 ? 0 : remainingMilliSeconds;

        el.innerHTML = moment.utc(remainingMilliSeconds).format(TIMERFORMAT);
    }, COUNTDOWNSTEPSECONDS * 1000);
});

function setStartTimestamp(remainingSeconds) {
    return Date.now() + remainingSeconds * 1000;
}
