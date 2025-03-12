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
  
  <template>
    <div class="flex justify-center items-center min-h-screen bg-gray-100">
      <div class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">Entrar</h2>
        <form @submit.prevent="handleLogin">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">E-mail</label>
            <input v-model="email" type="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required />
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
            <input v-model="password" type="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required />
          </div>
          <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition duration-200">
            Entrar
          </button>
        </form>
        <p v-if="errorMessage" class="text-red-500 text-center mt-4">{{ errorMessage }}</p>
        <p class="text-gray-600 text-center mt-4">
          Não tem uma conta? <router-link to="/register" class="text-blue-500 font-bold">Criar conta</router-link>
        </p>
      </div>
    </div>
  </template>