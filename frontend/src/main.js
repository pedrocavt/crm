import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import { createPinia } from "pinia";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import "./assets/main.css";

const app = createApp(App);
app.use(router);
app.use(createPinia());
app.use(Toast, {
  position: "top-right",
  timeout: 5000,
  closeOnClick: true,
  pauseOnHover: true,
  transition: "Vue-Toastification__bounce",
});
app.mount("#app");
