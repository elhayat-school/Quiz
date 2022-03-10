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
        <form onSubmit={loginHandler} className="flex flex-col items-center">
            <input
                type="email"
                value={email}
                onChange={(ev) => {
                    setEmail(ev.target.value.toLocaleLowerCase());
                }}
                className="w-[80%] h-8 p-1 m-1 rounded-sm border-gray-400"
                placeholder="email"
            />

            <input
                type="password"
                autoComplete=""
                value={password}
                onChange={(ev) => {
                    setPassword(ev.target.value);
                }}
                className="w-[80%] h-8 p-1 m-1 rounded-sm border-gray-400"
                placeholder="password"
            />

            <button className="w-[80%] bg-emerald-600 text-gray-50 m-1 px-4 py-2 rounded-sm font-bold">
                Connecter
            </button>
        </form>
    );
};

export default LoginForm;
