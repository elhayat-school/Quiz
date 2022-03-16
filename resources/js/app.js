require("./bootstrap");

import ReactDOM from "react-dom";
import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";

import softRedirect from "./services/softRedirect";
import redirect from "./services/redirect";
//
import LoginForm from "./components/LoginForm";
import PlayGround from "./components/PlayGround";

// Guest route
if (sessionStorage.getItem("auth_token") && location.pathname === "/login") {
    redirect("/play"); // softRedirect("/play");
}
// Auth routes
else if (
    !sessionStorage.getItem("auth_token") &&
    location.pathname === "/play"
) {
    redirect("/login"); // softRedirect("/login");
}

// To soft reload dispatch event and change state

ReactDOM.render(
    <div className="bg-stone-300 flex-1">
        <BrowserRouter>
            <Routes>
                <Route path="/play" element={<PlayGround />} />
                <Route path="/login" element={<LoginForm />} />
            </Routes>
        </BrowserRouter>
    </div>,
    document.getElementById("root")
);
