require("./bootstrap");

import ReactDOM from "react-dom";

import FrontEnd from "./FrontEnd";

import softRedirect from "./services/softRedirect";
import redirect from "./services/redirect";

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

ReactDOM.render(<FrontEnd />, document.getElementById("root"));
