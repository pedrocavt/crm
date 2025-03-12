import { defineStore } from "pinia";
import api from "../services/api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")) : null,
        token: localStorage.getItem("token") || null,
    }),
    actions: {
        async login(credentials) {
            try {
                const response = await api.post("/login", credentials);
                const { data } = response;

                if (!data.user || !data.token) {
                    throw new Error("Resposta inesperada da API");
                }

                this.user = data.user;
                this.token = data.token;

                localStorage.setItem("user", JSON.stringify(data.user));
                localStorage.setItem("token", data.token);

                api.defaults.headers.common["Authorization"] = `Bearer ${data.token}`;
            } catch (error) {
                console.error("Erro no login:", error);
                throw error;
            }
        },
        logout() {
            this.user = null;
            this.token = null;
            localStorage.removeItem("user");
            localStorage.removeItem("token");

            delete api.defaults.headers.common["Authorization"];
        },
    },
});
