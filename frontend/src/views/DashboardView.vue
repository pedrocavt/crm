<script setup>
import { ref, onMounted } from "vue";
import BaseLayout from "../components/BaseLayout.vue";
import { useAuthStore } from "../store/auth";
import { meetingService } from "../services/meetingService";
import { userService } from "../services/userService";
import echo from "../services/pusherService";
import { useToast } from "vue-toastification";

const authStore = useAuthStore();
const meetings = ref([]);
const users = ref([]);
const selectedUser = ref(null);
const isEditing = ref(false);
const toast = useToast();

const newMeeting = ref({
  id: null,
  user_id: authStore.user?.id || "",
  invited_user_id: "",
  scheduled_at: "",
  notes: "",
});

const fetchMeetings = async () => {
  try {
    meetings.value = await meetingService.getMeetings();
  } catch (error) {
    console.error("Erro ao carregar reuniões");
  }
};

const fetchUsers = async () => {
  try {
    const data = await userService.getUsers();
    users.value = data.filter(user => user.id !== authStore.user.id);
  } catch (error) {
    console.error("Erro ao carregar usuários");
  }
};

const selectUser = (user) => {
  selectedUser.value = user;
  newMeeting.value.invited_user_id = user.id;
};

const saveMeeting = async () => {
  try {
    if (isEditing.value) {
      await meetingService.updateMeeting(newMeeting.value.id, {
        scheduled_at: newMeeting.value.scheduled_at,
        notes: newMeeting.value.notes,
      });
    } else {
      await meetingService.createMeeting({
        user_id: authStore.user.id,
        invited_user_id: newMeeting.value.invited_user_id,
        scheduled_at: newMeeting.value.scheduled_at,
        notes: newMeeting.value.notes,
      });
    }

    fetchMeetings();
    closeModal();
  } catch (error) {
    console.error("Erro ao salvar reunião:", error);
  }
};

const closeModal = () => {
  selectedUser.value = null;
  isEditing.value = false;
  newMeeting.value = { id: null, user_id: authStore.user.id, invited_user_id: "", scheduled_at: "", notes: "" };
};

const editMeeting = (meeting) => {
  isEditing.value = true;
  newMeeting.value = {
    id: meeting.id,
    user_id: meeting.user_id,
    invited_user_id: meeting.invited_user_id,
    scheduled_at: meeting.scheduled_at,
    notes: meeting.notes,
  };
};

const deleteMeeting = async (id) => {
  try {
    await meetingService.deleteMeeting(id);
    meetings.value = meetings.value.filter(meeting => meeting.id !== id);
  } catch (error) {
    console.error("Erro ao excluir reunião");
  }
};

const listenForMeetings = () => {
  const channel = echo.private(`users.${authStore.user.id}`);

  channel.listen(".meeting.created", (data) => { 
    let notificationMessage = "";

    if (data.meeting.user_id === authStore.user.id) {
      notificationMessage = `Você marcou uma reunião com ${data.meeting.invited_user?.name || "alguém"}`;
    } else if (data.meeting.invited_user_id === authStore.user.id) {
      notificationMessage = `Nova reunião com ${data.meeting.user?.name || "convidado"}`;
    }

    toast.success(notificationMessage, {
      timeout: 5000,
      closeOnClick: true,
      pauseOnHover: true,
      position: "top-right",
    });

    fetchMeetings();
  });

  channel.error((error) => {
    console.error("Erro to connect channel pusher:", error);
  });
};

const listenForMeetingReminders = () => {
    const channel = echo.private(`users.${authStore.user.id}`);

    channel.listen(".meeting.reminder", (data) => {
        let name = data.meeting.user_id === authStore.user.id ? data.meeting.invited_user?.name : data.meeting.user?.name;
        const notificationMessage = `⏰ Lembrete: Você tem uma reunião com ${name} em breve!`;
        
        toast.info(notificationMessage, {
            timeout: 5000,
            closeOnClick: true,
            pauseOnHover: true,
            position: "top-right",
        });
    });
};

const listenForMeetingDeleteds = () => {
        const channel = echo.private(`users.${authStore.user.id}`);
        
        channel.listen(".meeting.deleted", (data) => {
          
          let name = data.meeting.user_id === authStore.user.id ? data.meeting.invited_user?.name : data.meeting.user?.name;
          const notificationMessage = `Aviso: Sua reunião com ${name} foi cancelada!`;

        toast.error(notificationMessage, {
            timeout: 5000,
            closeOnClick: true,
            pauseOnHover: true,
            position: "top-right",
        });

      fetchMeetings();
    });
};
      
const formatDate = (dateTime) => {
  return new Date(dateTime).toLocaleString("pt-BR");
};

onMounted(() => {
  fetchMeetings();
  fetchUsers();
  listenForMeetings();
  listenForMeetingReminders();
  listenForMeetingDeleteds();
});
</script>

<template>
  <BaseLayout>
    <div class="flex h-screen">
      <aside class="w-64 bg-blue-500 text-white p-6">
        <h2 class="text-lg font-bold mb-4">Usuários</h2>
        <ul>
          <li
            v-for="user in users"
            :key="user.id"
            @click="selectUser(user)"
            class="cursor-pointer hover:bg-blue-600 px-3 py-2 rounded-lg transition"
          >
            {{ user.name }}
          </li>
        </ul>
      </aside>

      <main class="flex-1 p-6">
        <h1 class="text-3xl font-bold text-gray-700 mb-6">Minhas Reuniões</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="meeting in meetings"
            :key="meeting.id"
            class="bg-white p-6 rounded-lg shadow-md cursor-pointer"
            @click="editMeeting(meeting)" 
          >
            <h3 class="text-lg font-bold text-gray-700">{{ formatDate(meeting.scheduled_at) }}</h3>
            <p class="text-gray-600"><strong>Organizador:</strong> {{ meeting.user?.name || "Não informado" }}</p>
            <p class="text-gray-600"><strong>Convidado:</strong> {{ meeting.invited_user?.name || "Não informado" }}</p>
            <p class="text-gray-600">{{ meeting.notes }}</p>
            <button @click.stop="deleteMeeting(meeting.id)" class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
              Excluir
            </button>
          </div>
        </div>
      </main>
    </div>

    <div v-if="selectedUser || isEditing" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-md">
        <h2 class="text-lg font-bold text-gray-700 mb-4">{{ isEditing ? "Editar Reunião" : "Agendar Reunião" }}</h2>

        <p v-if="!isEditing" class="text-gray-600">Você deseja marcar uma reunião com <strong>{{ selectedUser?.name }}</strong>?</p>
        
        <label class="block text-gray-700 text-sm font-bold mt-4">Data e Hora</label>
        <input v-model="newMeeting.scheduled_at" type="datetime-local" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400" required />
        
        <label class="block text-gray-700 text-sm font-bold mt-4">Notas</label>
        <textarea v-model="newMeeting.notes" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400"></textarea>

        <div class="mt-6 flex justify-between">
          <button @click="saveMeeting" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
            {{ isEditing ? "Salvar Alterações" : "Confirmar" }}
          </button>
          <button @click="closeModal" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-500">
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </BaseLayout>
</template>
