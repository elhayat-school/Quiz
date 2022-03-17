import React from "react";

// uncheck on update

const Choice = (props) => {
    return (
        <div className="bg-gray-50 m-2 px-2 py-4 rounded-md shadow-sm flex items-center flex-row-reverse">
            <input
                name="choice_number"
                type="radio"
                value={props.answer.choice_number}
                className="h-6 w-6 ml-2"
                required
            ></input>

            <label dir="rtl" className="flex-1">
                {props.answer.content}
            </label>
        </div>
    );
};

export default Choice;
