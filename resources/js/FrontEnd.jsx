import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
//
import LoginForm from "./components/LoginForm";
import PlayGround from "./components/PlayGround";

const FrontEnd = (props) => {
    return (
        <div className="bg-stone-300 flex-1">
            <BrowserRouter>
                <Routes>
                    <Route path="/play" element={<PlayGround />} />
                    <Route path="/login" element={<LoginForm />} />
                </Routes>
            </BrowserRouter>
        </div>
    );
};

export default FrontEnd;
