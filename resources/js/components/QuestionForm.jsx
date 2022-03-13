import React, { useState, useEffect } from "react";

import Choice from "./Choice";

function QuestionForm() {
    const [question, setQuestion] = useState([]);
    const [choices, setChoices] = useState([]);
    const [startFlag, setStartFlag] = useState(false);

    useEffect(() => {
        // check auth
        // Mount
        if (!startFlag) {
            getQuestion(
                "questions",
                successfulQuestionFetchHandler,
                failureFirstQuestionFetchHandler
            );
        }
    });

    /**
     *
     * @param {string} api
     * @param {callback} onSuccess
     * @param {callback} onFail
     */
    const getQuestion = (api, onSuccess, onFail) => {
        setStartFlag(true);

        axios.get("/sanctum/csrf-cookie").then(() => {
            axios
                .get(`/api/${api}`, {
                    headers: {
                        Authorization: `Bearer ${sessionStorage.getItem(
                            "auth_token"
                        )}`,
                    },
                })
                .then(onSuccess)
                .catch(onFail);
        });
    };

    /**
     *
     * @param {*} res
     */
    const successfulQuestionFetchHandler = (res) => {
        const data = res.data;
        setQuestion(data.question.content);
        setChoices(data.question.choices);
        //
        console.log(data.question.content);
        console.table(data.question.choices);
    };

    /**
     *
     * @param {*} err
     */
    const failureFirstQuestionFetchHandler = (err) => {
        // If 401 redirect to login
        setStartFlag(false);
    };

    /**
     *
     * @param {SubmitEvent} ev
     */
    const answerHandler = (ev) => {
        ev.preventDefault();
        // do more and use POST
        getQuestion("questions", successfulQuestionFetchHandler, (e) => {
            console.error(ev);
        });
    };

    /**
     *
     * @returns
     */
    const renderChoices = () => {
        return choices.map(function (choice) {
            return <Choice key={choice.nb} answer={choice} />;
        });
    };

    return (
        <form onSubmit={answerHandler} className="bg-white">
            <h2
                className="bg-gray-100 px-4 py-6 rounded-b-xl text-xl"
                dir="rtl"
            >
                {question}
            </h2>

            {renderChoices()}

            <button className="bg-emerald-600 text-gray-50 mx-2 px-4 py-2 rounded-full font-bold">
                Answer
            </button>
        </form>
    );
}

export default QuestionForm;