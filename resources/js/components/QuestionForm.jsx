import React, { useState, useEffect } from "react";

import Choice from "./Choice";
import CountDown from "./CountDown";

function QuestionForm() {
    console.group("%cQuestionForm scope{}", "background: #333; color: #1900ff");

    const [quizStatus, setQuizStatus] = useState("");
    const [startAt, setStartAt] = useState(false);

    const [question, setQuestion] = useState("");
    const [choices, setChoices] = useState([]);

    const [touchRerender, setTouchRerender] = useState(0); // update when in need to rerender

    /**
     * @param {string} api
     * @param {callback} onSuccess
     * @param {callback} onFail
     */
    const getQuestion = (api, onSuccess, onFail) => {
        console.group(
            "%cgetQuestion scope{}",
            "background: #333; color: #b260ff"
        );
        console.info("==>  ASKING THE API FOR A QUESTION");

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
        console.groupEnd("getQuestion scope{}");
    };

    /**
     * @param {*} res
     */
    const successfulQuestionFetchHandler = (res) => {
        console.group(
            "%csuccessfulQuestionFetchHandler scope{}",
            "background: #333; color: #22a7ff"
        );
        console.info("==>  GOT AN API RESPONSE FOR QUESTION");

        res.data.body.start_at = res.data.body.start_at * 1000; // to ms
        console.log(`====> QUIZ STATUS:  ->> ${res.data.status} <<-`);

        setQuizStatus(res.data.status);
        setStartAt(res.data.body.start_at);

        if (res.data.status === "PLAYING") {
            setQuestion(res.data.body.question.content);
            setChoices(res.data.body.question.choices);

            console.log("====> Question: ", res.data.body.question.content);
            console.table(res.data.body.question.choices);
        }

        console.info("==>  FINISHED HANDLING API RESPONSE FOR QUESTION");
        console.groupEnd("successfulQuestionFetchHandler scope{}");
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
        console.group(
            "%crenderCountDown scope{}",
            "background: #333; color: #bada55"
        );

        console.log("====> calling CountDown render helper: ", startAt);

        const el = <CountDown rdv={startAt} />;

        console.groupEnd("renderCountDown scope{}");
        return el;
    };

    /**
     * @returns
     */
    const renderChoices = () => {
        console.group(
            "%crenderChoices scope{}",
            "background: #333; color: #bada55"
        );

        const el = choices.map(function (choice) {
            return <Choice key={choice.choice_number} answer={choice} />;
        });

        console.groupEnd("renderChoices scope{}");
        return el;
    };

    /* ------------------------------- */
    //      USE EFFECT
    /* ------------------------------- */
    useEffect(() => {
        console.group(
            "%cuseEffect scope{}",
            "background: #333; color: #ff6060"
        );
        console.count("=> useEffect exec...");
        // check auth ?
        if (quizStatus === "" || (question === "" && choices.length === 0)) {
            console.log("==> QUIZ STATUS: ->> UNKNOWN <<- (MOUNT)");

            getQuestion(
                "questions",
                successfulQuestionFetchHandler,
                failureFirstQuestionFetchHandler
            );
        }

        console.groupEnd("useEffect scope{}");
    });

    const el = (
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

    console.groupEnd("QuestionForm scope{}");
    return el;
}

export default QuestionForm;
