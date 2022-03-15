import React, { useState, useEffect } from "react";

import Choice from "./Choice";
import CountDown from "./CountDown";

function QuestionForm() {
    const [question, setQuestion] = useState([]);
    const [choices, setChoices] = useState([]);

    const [quizStatus, setQuizStatus] = useState("");
    const [isRendered, setIsRendered] = useState(false);

    const [startAt, setStartAt] = useState(false);

    useEffect(() => {
        console.log("useEffect exec...");
        // check auth
        // Mount
        if (quizStatus === "") {
            getQuestion(
                "questions",
                successfulQuestionFetchHandler,
                failureFirstQuestionFetchHandler
            );
        }

        if (quizStatus !== "" && !isRendered) {
            //
        }
    });

    /**
     * @param {string} api
     * @param {callback} onSuccess
     * @param {callback} onFail
     */
    const getQuestion = (api, onSuccess, onFail) => {
        console.info("===>  ASKING API FOR QUESTION");

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
     * @param {*} res
     */
    const successfulQuestionFetchHandler = (res) => {
        console.info("===>  GOT API FOR QUESTION");

        res.data.body.start_at = res.data.body.start_at * 1000;
        console.log(`==> QUIZ STATE:  ->> ${res.data.status} <<-`);

        setQuizStatus(res.data.status);
        setStartAt(res.data.body.start_at);

        //
        // HERE ===================>
        //
        if (res.data.status === "PLAYING") {
            setQuestion(res.data.body.question.content);
            setChoices(res.data.body.question.choices);

            console.log("==> Question: ", res.data.body.question.content);
            console.table(res.data.body.question.choices);
            //
        } else if (res.data.status === "TOO_EARLY") {
            console.log(
                `==>  refresh in ${
                    (res.data.body.start_at - new Date().getTime()) / 1000
                } seconds`
            );

            setTimeout(() => {
                // UTC to local --> refresh to start playing
                location.reload();
            }, res.data.body.start_at - new Date().getTime());
        }

        console.info("===>  FINISHED HANDLING API RESPONSE FOR QUESTION");
    };

    /**
     * @param {*} err
     */
    const failureFirstQuestionFetchHandler = (err) => {
        // If 401 redirect to login
    };

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

    /* ------------------------------- */
    //      UI Body buidlers
    /* ------------------------------- */
    const renderCountDown = () => {
        console.log("calling CountDown render helper: ", startAt);
        return <CountDown rdv={startAt} />;
    };

    /**
     * @returns
     */
    const renderChoices = () => {
        return choices.map(function (choice) {
            return <Choice key={choice.choice_number} answer={choice} />;
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

            {quizStatus === "TOO_EARLY"
                ? renderCountDown()
                : quizStatus === "PLAYING"
                ? renderChoices()
                : quizStatus === "TOO_LATE"
                ? "trop tard"
                : ""}

            <button className="bg-emerald-600 text-gray-50 mx-2 px-4 py-2 rounded-full font-bold">
                Answer
            </button>
        </form>
    );
}

export default QuestionForm;
