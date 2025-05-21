import { createRouter, createWebHistory } from 'vue-router'
import Login from '../Views/Login.vue'
import Dashboard from '../Views/Dashboard.vue'
import Register from '../Views/Register.vue'

const routes = [
  { path: '/', name: 'dashboard', component: Dashboard },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/boards/:id', name: 'board', component: () => import('../Views/Board.vue'), props: true },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router