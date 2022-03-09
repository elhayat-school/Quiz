import axios from "axios";

export default axios.create({
    baseURL: `${window.location.origin}/api/`,
    headers: {
        Authorization: `Bearer ${sessionStorage.getItem("auth_token")}`,
    },
});
