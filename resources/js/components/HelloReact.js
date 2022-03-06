import React from "react";

const helloReact = (props) => {
    return (
        <div>
            <h2 className="text-center">Hello {props.name}!</h2>
            <p>{props.children}</p>
        </div>
    );
};

export default helloReact;
