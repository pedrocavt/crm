import { createRouter, createWebHistory } from "vue-router";

const routes = [
    { path: "/", redirect: "/login" },
    { path: "/login", component: () => import("../views/LoginView.vue") },
    { path: "/register", component: () => import("../views/RegisterView.vue") },
    { path: "/dashboard", component: () => import("../views/DashboardView.vue"), meta: { requiresAuth: true } },
    { path: "/meetings", component: () => import("../views/MeetingsView.vue"), meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem("token");
    if (to.meta.requiresAuth && !isAuthenticated) {
        next("/login");
    } else {
        next();
    }
});

export default router;
