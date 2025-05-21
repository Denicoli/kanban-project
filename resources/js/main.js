import { createApp } from 'vue'
import App from './App.vue'
import router from './Router'
import { User, Plus, Eye, Pencil, Trash } from 'lucide-vue-next'

const app = createApp(App)

const icons = { User, Plus, Eye, Pencil, Trash }

Object.entries(icons).forEach(([name, component]) => {
    app.component(`${name}Icon`, component)
})

app.use(router)
app.mount('#app')