<script setup>
import { ref } from "vue";
import { useAuthStore } from "../store/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");
const errorMessage = ref("");

const handleRegister = async () => {
    try {
        await authStore.register({
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: password_confirmation.value,
        });
        router.push("/dashboard");
    } catch (error) {
        errorMessage.value = "Erro ao criar conta.";
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center text-gray-700">Criar Conta</h2>

            <form @submit.prevent="handleRegister" class="mt-6">
                <div class="mb-4">
                    <label class="block text-gray-600">Nome</label>
                    <input
                        type="text"
                        v-model="name"
                        class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
                        required
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600">E-mail</label>
                    <input
                        type="email"
                        v-model="email"
                        class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
                        required
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600">Senha</label>
                    <input
                        type="password"
                        v-model="password"
                        class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
                        required
                    />
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600">Confirmar Senha</label>
                    <input
                        type="password"
                        v-model="password_confirmation"
                        class="w-full px-4 py-2 mt-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-300"
                        required
                    />
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200"
                >
                    Criar Conta
                </button>
            </form>

            <p class="text-center text-gray-600 mt-4">
                Já tem uma conta? 
                <router-link to="/login" class="text-blue-500 hover:underline">
                    Faça login
                </router-link>
            </p>

            <p v-if="errorMessage" class="text-red-500 text-center mt-2">{{ errorMessage }}</p>
        </div>
    </div>
</template>
