import React, { useState } from "react";
//
import HelloReact from "./components/HelloReact";

const frontEnd = (props) => {
    const [frontEndState, frontEndSetState] = useState({
        players: ["mediliess", "abdimed", "scrumbo"],
    });

    const rotate = () => {
        let temp = frontEndState.players;
        temp.push(temp[0]);
        temp.shift();
        frontEndSetState({
            players: temp,
            // manually add other state
        });
    };

    return (
        <div>
            <h1 className="text-center font-bold"> ROOT </h1>
            <HelloReact name={frontEndState.players[0]}>
                = Laravel commiter :3
            </HelloReact>
            <HelloReact name={frontEndState.players[1]} />
            <HelloReact name={frontEndState.players[2]} />
            <button onClick={rotate}> rotate </button>
        </div>
    );
};

export default frontEnd;
