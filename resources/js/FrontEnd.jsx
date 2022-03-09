import React, { useState, useEffect } from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
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
            <BrowserRouter>
                <Routes>
                    <Route path="/play" element={<QuestionForm />} />
                    <Route path="/login" element={<LoginForm />} />
                </Routes>
            </BrowserRouter>
        </div>
    );
};

export default FrontEnd;
