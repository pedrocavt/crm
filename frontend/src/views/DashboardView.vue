<template>
    <div>
        <h1>Bem-vindo ao Dashboard, {{ authStore.user?.name }}!</h1>
        <button @click="handleLogout">Sair</button>
    </div>
</template>

<script setup>
import { useAuthStore } from "../store/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = () => {
    authStore.logout();
    router.push("/login");
};
</script>

<style scoped>
h1 {
    color: #333;
}
button {
    margin-top: 1rem;
    padding: 0.5rem;
    cursor: pointer;
}
</style>
