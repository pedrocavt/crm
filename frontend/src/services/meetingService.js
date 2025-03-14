import api from "../services/api";

export const meetingService = {
  async getMeetings() {
    try {
      const { data } = await api.get("/meetings");
      return data;
    } catch (error) {
      console.error("Erro ao buscar reuniões:", error);
      throw error;
    }
  },

  async createMeeting(meetingData) {
    try {
      const { data } = await api.post("/meetings", meetingData);
      return data;
    } catch (error) {
      console.error("Erro ao criar reunião:", error);
      throw error;
    }
  },

  async updateMeeting(meetingId, meetingData) {
    try {
      const { data } = await api.put(`/meetings/${meetingId}`, meetingData);
      return data;
    } catch (error) {
      console.error("Erro ao atualizar reunião:", error);
      throw error;
    }
  },

  async deleteMeeting(meetingId) {
    try {
      await api.delete(`/meetings/${meetingId}`);
    } catch (error) {
      console.error("Erro ao excluir reunião:", error);
      throw error;
    }
  }
};
