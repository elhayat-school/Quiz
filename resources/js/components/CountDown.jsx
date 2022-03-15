import React from "react";
// import { useState } from "react";

const CountDown = (props) => {
    console.log("called CountDown render helper: ", props.rdv);
    // const [startAt, setStartAt] = useState(props.rdv);

    // setInterval(() => {
    //     console.log(startAt);
    //     setStartAt(startAt - 2);
    // }, 2000);

    if (props.rdv !== false) {
        return (
            <div>
                <span>{new Date(Math.abs(props.rdv)).getHours()}</span>:
                <span>{new Date(Math.abs(props.rdv)).getMinutes()}</span>
                <span> </span>
                <span>{new Date(Math.abs(props.rdv)).getDate()}</span>/
                <span>{new Date(Math.abs(props.rdv)).getMonth()}</span>/
                <span>{new Date(Math.abs(props.rdv)).getFullYear()}</span>
            </div>
        );
    } else {
        return <div></div>;
    }
};

export default CountDown;
