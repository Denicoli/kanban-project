import { createApp } from 'vue'
import App from './App.vue'
import router from './Router'

import {
    User,
    Plus,
    Eye,
    Pencil,
    Trash,
    Calendar,
    ChevronLeft,
    EllipsisVertical,
    Ellipsis,
} from 'lucide-vue-next'

const app = createApp(App)

const icons = {
    User,
    Plus,
    Eye,
    Pencil,
    Trash,
    Calendar,
    ChevronLeft,
    EllipsisVertical,
    Ellipsis,
}

for (const [name, component] of Object.entries(icons)) {
    app.component(`${name}Icon`, component)
}

app.use(router)
app.mount('#app')