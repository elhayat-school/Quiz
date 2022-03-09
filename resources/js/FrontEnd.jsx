import React, { useState, useEffect } from "react";
//
import QuestionForm from "./components/QuestionForm";
import LoginForm from "./components/LoginForm";

const FrontEnd = (props) => {
    const [time, setTime] = useState(false);

    useEffect(() => {
        if (!time) {
            const clock = setInterval(() => {
                setTime(new Date().toLocaleString());
            }, 1000);
        }
    });

    return (
        <div className="bg-stone-300 flex-1">
            <h2 className="text-center font-bold"> {time} </h2>

            <LoginForm />
            <QuestionForm />
        </div>
    );
};

export default FrontEnd;
