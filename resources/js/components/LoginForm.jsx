import React, { useState } from "react";
import { useNavigate } from "react-router-dom";

const LoginForm = () => {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const navigate = useNavigate();

    /**
     *
     * @param {SubmitEvent} ev
     */
    const loginHandler = (ev) => {
        ev.preventDefault();

        if (email.length > 0 && password.length > 0) {
            axios.get("/sanctum/csrf-cookie").then(() => {
                axios
                    .post("api/login", {
                        email: email,
                        password: password,
                    })
                    .then((response) => {
                        console.log(response.data);
                        sessionStorage.setItem(
                            "auth_token",
                            response.data.token
                        );
                        navigate("/play");
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            });
        }
    };

    return (
        <form onSubmit={loginHandler}>
            <input
                type="email"
                value={email}
                onChange={(ev) => {
                    setEmail(ev.target.value.toLocaleLowerCase());
                }}
                className="w-full h-8 p-1 m-1 rounded-sm border-gray-400"
                placeholder="email"
            />

            <input
                type="password"
                autoComplete=""
                value={password}
                onChange={(ev) => {
                    setPassword(ev.target.value);
                }}
                className="w-full h-8 p-1 m-1 rounded-sm border-gray-400"
                placeholder="password"
            />

            <button className="bg-emerald-600 text-gray-50 mx-2 px-4 py-2 rounded-full font-bold">
                Connecter
            </button>
        </form>
    );
};

export default LoginForm;
