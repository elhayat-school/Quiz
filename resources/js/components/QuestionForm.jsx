import React, { useState } from "react";
import Countdown from "react-countdown";

import Choice from "./Choice";

function QuestionForm(props) {
    console.group("%cQuestionForm scope{}", "background: #333; color: #1900ff");

    const [questionContent, setQuestionContent] = useState(
        props.questionContent
    );
    const [questionId, setQuestionId] = useState(props.questionId);
    const [questionDuration, setQuestionDuration] = useState(
        props.questionDuration
    );

    const [choices, setChoices] = useState(props.choices);

    // -----------------------------------------------------------------
    // -----------------------------------------------------------------
    /**
     * @param {string} api
     * @param {callback} onSuccess
     * @param {callback} onFail
     */
    const postAnswer = (api, data, onSuccess, onFail) => {
        console.group(
            "%cpostAnswer scope{}",
            "background: #333; color: #b260ff"
        );
        console.log("==>  ASKING THE API FOR A QUESTION");

        axios.get("/sanctum/csrf-cookie").then(() => {
            axios
                .post(`/api/${api}`, data, {
                    headers: {
                        Authorization: `Bearer ${sessionStorage.getItem(
                            "auth_token"
                        )}`,
                    },
                })
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
            console.table(res.data.body.question.choices);

            res.data.body.question.duration =
                res.data.body.question.duration * 1000; // to ms

            setQuestionContent(res.data.body.question.content);
            setQuestionDuration(res.data.body.question.duration);
            setQuestionId(res.data.body.question.id);
            setChoices(res.data.body.question.choices);
        } else if (res.data.status === "FINISHED") {
            location.reload(); // =============================>
        }

        console.log("==>  FINISHED HANDLING API RESPONSE FOR QUESTION");
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

        const answerData = new FormData(ev.target);
        console.log("==>  Answering: ");
        console.log(`===>  question_id: ${answerData.get("question_id")}`);
        console.log(`===>  choice_number: ${answerData.get("choice_number")}`);

        postAnswer(
            "anwsers",
            answerData,
            successfulAnswerPostHandler,
            failureAnswerPostHandler
        );
    };

    const el = (
        <form onSubmit={answerHandler} className="bg-white">
            <input type="hidden" name="question_id" value={questionId} />

            <h2
                dir="rtl"
                className="bg-gray-100 px-4 py-6 rounded-b-xl text-xl"
            >
                {questionContent}
            </h2>

            <Countdown date={Date.now() + questionDuration} />

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
