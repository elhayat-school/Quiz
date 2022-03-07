import React from "react";

function choice(props) {
    return (
        <div className="bg-gray-50 m-2 px-2 py-4 rounded-md shadow-sm flex item-center">
            <input
                name="answer"
                type="radio"
                value={props.answer.nb}
                className="h-6 w-6 mr-2"
            ></input>

            <label> {props.answer.content} </label>
        </div>
    );
}

export default choice;
