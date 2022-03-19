import React, { useState } from "react";
import ReactDOM from "react-dom";

import QuestionForm from "./QuestionForm";
import CountDown from "./CountDown";

const PlayGround = () => {
    console.group(
        "%cQuestionForm PlayGround{}",
        "background: #333; color: #1900ff"
    );

    const [touchToUpdate, setTouchToUpdate] = useState(0);

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

    // -----------------------------------------------------------------
    // -----------------------------------------------------------------

    /**
     * @param {*} res
     */
    const successfulQuestionFetchHandler = (res) => {
        console.group(
            "%csuccessfulQuestionFetchHandler scope{}",
            "background: #333; color: #22a7ff"
        );
        console.log("==>  GOT AN API RESPONSE FOR QUESTION", res.data);
        res.data.body.start_at = res.data.body.start_at * 1000; // to ms

        console.log(`====> QUIZ STATUS:  ->> ${res.data.status} <<-`);

        if (res.data.status === "PLAYING") {
            res.data.body.question.duration =
                res.data.body.question.duration * 1000; // to ms

            renderQuestionForm(
                componentId,
                res.data.body.question.content,
                res.data.body.question.id,
                res.data.body.question.duration,
                res.data.body.question.choices
            );
        } else if (res.data.status === "TOO_EARLY") {
            console.log(
                `====>  Refetching in ${
                    (res.data.body.start_at - new Date().getTime()) / 1000
                } seconds`
            );

            setTimeout(() => {
                // Timezone ?
                setTouchToUpdate(touchToUpdate + 1);
            }, res.data.body.start_at - new Date().getTime());

            renderCountDown(componentId, res.data.body.start_at);
        } else if (res.data.status === "TOO_LATE") {
            renderTooLate(componentId);
        }

        console.info("==>  FINISHED HANDLING API RESPONSE FOR QUESTION");
        console.groupEnd("successfulQuestionFetchHandler scope{}");
    };

    /**
     * @param {*} err
     */
    const failureFirstQuestionFetchHandler = (err) => {
        // If 401 redirect to login
        console.error("axios error", err);
    };

    /* ---------------------------------------------- */
    //                  ACTIONS
    /* ---------------------------------------------- */

    const componentId = "playGround";

    getQuestion(
        "questions",
        successfulQuestionFetchHandler,
        failureFirstQuestionFetchHandler
    );

    return <div id={componentId}> Loading... </div>;
};

/* ---------------------------------------------- */
//                  CONTENT REDERERS
/* ---------------------------------------------- */

/**
 * @param {string} componentId
 * @param {int} timestamp
 */
function renderCountDown(componentId, timestamp) {
    ReactDOM.render(
        <CountDown date={timestamp} />,
        document.getElementById(componentId)
    );
}

/**
 * @param {string} componentId
 * @param {string} content
 * @param {array} choices
 */
function renderQuestionForm(
    componentId,
    content,
    id,
    questionDuration,
    choices
) {
    console.log("====> Question: ", content);
    console.table(choices);

    ReactDOM.render(
        <QuestionForm
            questionContent={content}
            questionId={id}
            questionDuration={questionDuration}
            choices={choices}
        />,
        document.getElementById(componentId)
    );
}

/**
 * @param {string} componentId
 */
function renderTooLate(componentId) {
    ReactDOM.render("Le quiz est fini", document.getElementById(componentId));
}

export default PlayGround;
