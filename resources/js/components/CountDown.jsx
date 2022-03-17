import React from "react";
import Countdown from "react-countdown";

const CountDown = (props) => {
    console.group("%cCountDown scope{}", "background: #333; color: #bada55");
    console.log("====> Rendering Coundown: ");

    console.groupEnd("CountDown scope{}");

    return <Countdown date={props.date} />;
};

export default CountDown;
