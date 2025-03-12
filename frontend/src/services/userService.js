import api from "../services/api";

export const userService = {
  async getUsers() {
    try {
      const { data } = await api.get("/users");
      return data;
    } catch (error) {
      console.error("Erro ao buscar usuários:", error);
      throw error;
    }
  }
};
