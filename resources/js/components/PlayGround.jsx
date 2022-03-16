import React from "react";

import ReactDOM from "react-dom";

import QuestionForm from "./QuestionForm";
import Countdown from "react-countdown";

const PlayGround = () => {
    console.group(
        "%cQuestionForm PlayGround{}",
        "background: #333; color: #1900ff"
    );

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
            //
            console.log("====> Question: ", res.data.body.question.content);
            console.table(res.data.body.question.choices);

            ReactDOM.render(
                <QuestionForm
                    question={res.data.body.question.content}
                    choices={res.data.body.question.choices}
                />,
                document.getElementById("playGround")
            );
        }
        //
        else if (res.data.status === "TOO_EARLY") {
            //
            ReactDOM.render(
                <Countdown date={res.data.body.start_at} />,
                document.getElementById("playGround")
            );

            console.log("====> Rendered Coundown: ");
            console.log(
                `====>  refresh in ${
                    (res.data.body.start_at - new Date().getTime()) / 1000
                } seconds`
            );

            // Replace this reload with a fetch/render
            setTimeout(() => {
                // UTC to local --> refresh to start playing
                location.reload();
            }, res.data.body.start_at - new Date().getTime());
            //
        }
        //
        else if (res.data.status === "TOO_LATE") {
            //
            ReactDOM.render(
                "Le quiz est fini",
                document.getElementById("playGround")
            );
        }

        console.info("==>  FINISHED HANDLING API RESPONSE FOR QUESTION");
        console.groupEnd("successfulQuestionFetchHandler scope{}");
    };

    getQuestion(
        "questions",
        successfulQuestionFetchHandler,
        failureFirstQuestionFetchHandler
    );

    /**
     * @param {*} err
     */
    const failureFirstQuestionFetchHandler = (err) => {
        // If 401 redirect to login
    };

    return <div id="playGround"> Loading... </div>;
};

export default PlayGround;
