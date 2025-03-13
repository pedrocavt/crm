import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;
Pusher.logToConsole = true;

const echo = new Echo({
    broadcaster: "pusher",
    key: "11ab48234deaf06272ad",
    cluster: "sa1",
    forceTLS: true,
    encrypted: true,
    authEndpoint: "http://localhost:8000/api/broadcasting/auth",
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token") || ""}`,
            Accept: "application/json",
        },
    },
});

export default echo;
