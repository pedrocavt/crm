<template>
    <div class="login-container">
        <h2>Login</h2>
        <form @submit.prevent="handleLogin">
            <input type="email" v-model="email" placeholder="E-mail" required />
            <input type="password" v-model="password" placeholder="Senha" required />
            <button type="submit">Entrar</button>
        </form>
        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "../store/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const email = ref("");
const password = ref("");
const errorMessage = ref("");

const handleLogin = async () => {
    try {
        await authStore.login({ email: email.value, password: password.value });
        router.push("/dashboard");
    } catch (error) {
        errorMessage.value = "Credenciais inválidas.";
    }
};
</script>

<style scoped>
.login-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem;
}

input {
    display: block;
    width: 100%;
    margin-bottom: 1rem;
    padding: 0.5rem;
}

button {
    padding: 0.5rem;
    cursor: pointer;
}

.error {
    color: red;
}
</style>
