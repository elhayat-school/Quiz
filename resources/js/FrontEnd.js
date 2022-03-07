import React, { useState } from "react";
//
import QuestionCard from "./components/QuestionCard";

const frontEnd = (props) => {
    return (
        <div className="bg-stone-300 flex-1">
            <h2 className="text-center font-bold"> ROOT </h2>

            <QuestionCard />
        </div>
    );
};

export default frontEnd;
