import React from "react";
import Countdown from "react-countdown";

const CountDown = (props) => {
    console.group("%cCountDown scope{}", "background: #333; color: #bada55");
    console.log("====> Rendering Coundown: ");

    console.log(
        `====>  refresh in ${
            (props.date - new Date().getTime()) / 1000
        } seconds`
    );

    // Replace this reload with a fetch/render
    setTimeout(() => {
        // UTC to local --> refresh to start playing
        location.reload();
    }, props.date - new Date().getTime());

    console.groupEnd("CountDown scope{}");

    return <Countdown date={props.date} />;
};

export default CountDown;
