<script setup>
import { useAuthStore } from "../store/auth";
import { useRouter } from "vue-router";
import BaseLayout from "../components/BaseLayout.vue";

const authStore = useAuthStore();
const router = useRouter();

const handleLogout = () => {
    authStore.logout();
    router.push("/login");
};
</script>

<template>
  <BaseLayout>
    <h1 class="text-3xl font-bold text-gray-700">Dashboard</h1>
    <p class="text-gray-600">Bem-vindo, <span class="font-bold">{{ authStore.user?.name }}</span>!</p>
  </BaseLayout>
</template>