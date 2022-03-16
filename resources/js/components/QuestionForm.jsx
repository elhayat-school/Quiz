import React, { useState } from "react";

import Choice from "./Choice";

function QuestionForm(props) {
    console.group("%cQuestionForm scope{}", "background: #333; color: #1900ff");

    const [question, setQuestion] = useState(props.question);
    const [choices, setChoices] = useState(props.choices);

    /**
     * @param {SubmitEvent} ev
     */
    const answerHandler = (ev) => {
        ev.preventDefault();

        // do more and use POST

        getQuestion("questions", successfulQuestionFetchHandler, (e) => {
            console.error(ev);
        });
    };

    const el = (
        <form onSubmit={answerHandler} className="bg-white">
            <h2
                className="bg-gray-100 px-4 py-6 rounded-b-xl text-xl"
                dir="rtl"
            >
                {question}
            </h2>

            {choices.map(function (choice) {
                return <Choice key={choice.choice_number} answer={choice} />;
            })}

            <button className="bg-emerald-600 text-gray-50 mx-2 px-4 py-2 rounded-full font-bold">
                Answer
            </button>
        </form>
    );

    console.groupEnd("QuestionForm scope{}");
    return el;
}

export default QuestionForm;
