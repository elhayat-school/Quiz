import React, { useState } from "react";

import Choice from "./Choice";

function QuestionForm(props) {
    console.group("%cQuestionForm scope{}", "background: #333; color: #1900ff");

    const [question, setQuestion] = useState(props.question);
    const [choices, setChoices] = useState(props.choices);

    // -----------------------------------------------------------------
    // -----------------------------------------------------------------
    /**
     * @param {string} api
     * @param {callback} onSuccess
     * @param {callback} onFail
     */
    const postAnswer = (api, onSuccess, onFail) => {
        console.group(
            "%cpostAnswer scope{}",
            "background: #333; color: #b260ff"
        );
        console.info("==>  ASKING THE API FOR A QUESTION");

        axios.get("/sanctum/csrf-cookie").then(() => {
            axios
                .post(
                    `/api/${api}`,
                    {}, // get selected answer
                    {
                        headers: {
                            Authorization: `Bearer ${sessionStorage.getItem(
                                "auth_token"
                            )}`,
                        },
                    }
                )
                .then(onSuccess)
                .catch(onFail);
        });
        console.groupEnd("postAnswer scope{}");
    };

    /**
     * @param {*} res
     */
    const successfulAnswerPostHandler = (res) => {
        console.group(
            "%csuccessfulAnswerPostHandler scope{}",
            "background: #333; color: #22a7ff"
        );
        console.log("==>  GOT AN API RESPONSE FOR QUESTION", res.data);
        res.data.body.start_at = res.data.body.start_at * 1000; // to ms

        console.log(`====> QUIZ STATUS:  ->> ${res.data.status} <<-`);

        if (res.data.status === "PLAYING") {
            setQuestion(res.data.body.question.content);
            setChoices(res.data.body.question.choices);
        } else {
            location.reload();
        }

        console.info("==>  FINISHED HANDLING API RESPONSE FOR QUESTION");
        console.groupEnd("successfulAnswerPostHandler scope{}");
    };

    /**
     * @param {*} err
     */
    const failureAnswerPostHandler = (err) => {
        // If 401 redirect to login
    };

    /* ---------------------------------------------- */
    //                  ACTIONS
    /* ---------------------------------------------- */

    /**
     * @param {SubmitEvent} ev
     */
    const answerHandler = (ev) => {
        ev.preventDefault();

        postAnswer(
            "anwsers",
            successfulAnswerPostHandler,
            failureAnswerPostHandler
        );
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
