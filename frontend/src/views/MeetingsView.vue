<template>
  <BaseLayout>
    <div class="max-w-4xl mx-auto">
      <h1 class="text-3xl font-bold text-gray-700 mb-6">Gerenciamento de Reuniões</h1>

      <!-- Formulário de Agendamento -->
      <form @submit.prevent="createMeeting" class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-lg font-bold mb-4">Agendar Nova Reunião</h2>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Usuário Convidado (ID)</label>
          <input v-model="newMeeting.invited_user_id" type="number" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Data e Hora</label>
          <input v-model="newMeeting.scheduled_at" type="datetime-local" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-bold mb-2">Notas</label>
          <textarea v-model="newMeeting.notes" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400"></textarea>
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">
          Criar Reunião
        </button>
      </form>

      <!-- Lista de Reuniões -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-bold mb-4">Minhas Reuniões</h2>
        <div v-if="meetings.length === 0" class="text-gray-500 text-center">
          Nenhuma reunião agendada.
        </div>
        <ul v-else>
          <li v-for="meeting in meetings" :key="meeting.id" class="border-b py-4 flex justify-between items-center">
            <div>
              <p class="font-bold">{{ formatDate(meeting.scheduled_at) }}</p>
              <p class="text-gray-600">Convidado: <span class="font-semibold">{{ meeting.invited_user_id }}</span></p>
              <p class="text-gray-600">{{ meeting.notes }}</p>
            </div>
            <button @click="deleteMeeting(meeting.id)" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
              Excluir
            </button>
          </li>
        </ul>
      </div>
    </div>
  </BaseLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../services/api";
import BaseLayout from "../components/BaseLayout.vue";
import { useAuthStore } from "../store/auth";

const authStore = useAuthStore(); // Obtém o usuário autenticado
const meetings = ref([]);
const newMeeting = ref({
  user_id: authStore.user?.id || "", // Obtém o ID do usuário autenticado
  invited_user_id: "",
  scheduled_at: "",
  notes: "",
});

// Função para buscar reuniões
const fetchMeetings = async () => {
  try {
    const { data } = await api.get("/meetings");
    meetings.value = data;
  } catch (error) {
    console.error("Erro ao buscar reuniões:", error);
  }
};

// Função para criar uma reunião
const createMeeting = async () => {
  try {
    await api.post("/meetings", newMeeting.value);
    fetchMeetings(); // Atualiza a lista
    newMeeting.value = { user_id: authStore.user?.id || "", invited_user_id: "", scheduled_at: "", notes: "" };
  } catch (error) {
    console.error("Erro ao criar reunião:", error);
  }
};

// Função para excluir uma reunião
const deleteMeeting = async (id) => {
  try {
    await api.delete(`/meetings/${id}`);
    meetings.value = meetings.value.filter(meeting => meeting.id !== id);
  } catch (error) {
    console.error("Erro ao excluir reunião:", error);
  }
};

// Função para formatar data e hora
const formatDate = (dateTime) => {
  return new Date(dateTime).toLocaleString("pt-BR");
};

// Buscar reuniões ao carregar a página
onMounted(fetchMeetings);
</script>
