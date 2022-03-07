import React, { useState } from "react";
import Choice from "./Choice";

function questionCard() {
    const choices = [
        { nb: 1, content: "ching chang chong" },
        { nb: 2, content: "chang chong ching" },
        { nb: 3, content: "chang ching chong" },
        { nb: 4, content: "ching chong chang" },
    ];

    return (
        <div className="bg-white">
            <h2 className="bg-gray-100 px-4 py-6 rounded-b-xl text-xl">
                Question text
            </h2>

            <Choice answer={choices[0]} />
            <Choice answer={choices[1]} />
            <Choice answer={choices[2]} />
            <Choice answer={choices[3]} />

            <button className="bg-emerald-600 text-gray-50 mx-2 px-4 py-2 rounded-full font-bold">
                Answer
            </button>
        </div>
    );
}

export default questionCard;
