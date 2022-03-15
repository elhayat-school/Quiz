import React from "react";
// import { useState } from "react";

const CountDown = (props) => {
    console.group("CountDown scope{}");

    if (props.rdv === false) return <div></div>;

    const rdv = new Date(Math.abs(props.rdv));

    console.log("====> called CountDown render helper: ", props.rdv);
    console.log(
        `====>  refresh in ${(props.rdv - new Date().getTime()) / 1000} seconds`
    );

    setTimeout(() => {
        // UTC to local --> refresh to start playing
        location.reload();
    }, props.rdv - new Date().getTime());

    var el = (
        <div>
            <span>{rdv.getHours()}</span>:<span>{rdv.getMinutes()}</span>
            <span> </span>
            <span>{rdv.getDate()}</span>/<span>{rdv.getMonth()}</span>/
            <span>{rdv.getFullYear()}</span>
        </div>
    );

    console.groupEnd("CountDown scope{}");

    return el;
};

export default CountDown;
