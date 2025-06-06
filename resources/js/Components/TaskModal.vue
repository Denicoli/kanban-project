<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transition-all">
      <h4 class="font-bold text-lg mb-6 text-gray-800" id="modal-title">{{ task ? 'Edit Task' : 'New Task' }}</h4>
      <form @submit.prevent="submit" class="space-y-4" novalidate>
        <div>
          <label for="task-title" class="block text-sm font-medium mb-1 text-gray-700">Title</label>
          <input
            id="task-title"
            ref="titleInput"
            v-model="form.title"
            class="border border-gray-300 rounded-lg w-full px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
            placeholder="Task title"
            required
          />
          <p v-if="titleError" id="title-error" class="text-xs text-red-600 mt-1">{{ titleError }}</p>
        </div>
        <div>
          <label for="task-desc" class="block text-sm font-medium mb-1 text-gray-700">Description</label>
          <textarea
            id="task-desc"
            v-model="form.description"
            class="border border-gray-300 rounded-lg w-full px-3 py-2 mb-1 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
            placeholder="Task description"
            rows="2"
          />
        </div>
        <div>
          <label for="task-due" class="block text-sm font-medium mb-1 text-gray-700">Due Date</label>
          <input
            id="task-due"
            v-model="form.due_date"
            type="date"
            class="border border-gray-300 rounded-lg w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
            ref="dueDateInput"
            @mousedown.prevent="openDatePicker"
          />
        </div>
        <!-- Status Chips -->
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Status</label>
          <div class="flex flex-wrap gap-2 mt-1">
            <button
              v-for="option in statusOptions"
              :key="option.value"
              type="button"
              @click="setStatus(option.value)"
              :class="optionButtonClass(form.status === option.value, option)"
            >
              {{ option.label }}
            </button>
          </div>
        </div>
        <!-- Priority Chips -->
        <div>
          <label class="block text-sm font-medium mb-1 text-gray-700">Priority</label>
          <div class="flex flex-wrap gap-2 mt-1">
            <button
              v-for="option in priorityOptions"
              :key="option.value"
              type="button"
              @click="setPriority(option.value)"
              :class="optionButtonClass(form.priority === option.value, option)"
            >
              {{ option.label }}
            </button>
          </div>
        </div>
        <div class="flex justify-end gap-3 pt-4">
          <button @click="close" type="button" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow transition">
            {{ task ? 'Save' : 'Create' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, nextTick } from 'vue'

const dueDateInput = ref(null)
const titleInput = ref(null)
const titleError = ref('')

const props = defineProps({
  isOpen: Boolean,
  task: Object
})
const emit = defineEmits(['close', 'submit'])

const form = ref({
  title: props.task?.title || '',
  description: props.task?.description || '',
  due_date: toLocaleDateString(props.task?.due_date),
  status: props.task?.status || 'not-started',
  priority: props.task?.priority || 'low'
})

watch(() => props.task, (val) => {
  form.value = {
    title: val?.title || '',
    description: val?.description || '',
    due_date: toLocaleDateString(val?.due_date),
    status: val?.status || 'not-started',
    priority: val?.priority || 'low'
  }
})

watch(() => props.isOpen, (open) => {
  if (open) {
    nextTick(() => {
      titleInput.value?.focus()
    })
  }
})

function toLocaleDateString(dateStr) {
  if (!dateStr) return ''

  const date = new Date(dateStr)
  const yyyy = date.getUTCFullYear()
  const mm = String(date.getUTCMonth() + 1).padStart(2, '0')
  const dd = String(date.getUTCDate()).padStart(2, '0')
  return `${yyyy}-${mm}-${dd}`
}

const close = () => emit('close')
const submit = () => {
  if (!form.value.title.trim()) {
    titleError.value = 'Title is required.'
    titleInput.value?.focus()
    return
  }
  titleError.value = ''
  emit('submit', { ...form.value })
  close()
}
const openDatePicker = () => {
  if (dueDateInput.value) {
    dueDateInput.value.showPicker?.()
    dueDateInput.value.focus()
  }
}

const setStatus = (value) => {
  form.value.status = value
}
const setPriority = (value) => {
  form.value.priority = value
}
const optionButtonClass = (selected, option) => [
  'px-3 py-1 rounded-full text-xs font-semibold focus:outline-none transition',
  selected ? option.selected : option.unselected
]

const statusOptions = [
  {
    value: 'not-started',
    label: 'Not Started',
    selected: 'bg-purple-100 text-purple-700 ring-2 ring-purple-400',
    unselected: 'bg-purple-50 text-purple-400 hover:bg-purple-100'
  },
  {
    value: 'in-research',
    label: 'In Research',
    selected: 'bg-yellow-100 text-yellow-700 ring-2 ring-yellow-400',
    unselected: 'bg-yellow-50 text-yellow-400 hover:bg-yellow-100'
  },
  {
    value: 'on-track',
    label: 'On Track',
    selected: 'bg-pink-100 text-pink-700 ring-2 ring-pink-400',
    unselected: 'bg-pink-50 text-pink-400 hover:bg-pink-100'
  },
  {
    value: 'code-review',
    label: 'Code Review',
    selected: 'bg-blue-100 text-blue-700 ring-2 ring-blue-400',
    unselected: 'bg-blue-50 text-blue-400 hover:bg-blue-100'
  },
  {
    value: 'completed',
    label: 'Complete',
    selected: 'bg-green-100 text-green-700 ring-2 ring-green-400',
    unselected: 'bg-green-50 text-green-400 hover:bg-green-100'
  }
]

const priorityOptions = [
  {
    value: 'low',
    label: 'Low',
    selected: 'bg-blue-100 text-blue-700 ring-2 ring-blue-400',
    unselected: 'bg-blue-50 text-blue-400 hover:bg-blue-100'
  },
  {
    value: 'medium',
    label: 'Medium',
    selected: 'bg-yellow-100 text-yellow-700 ring-2 ring-yellow-400',
    unselected: 'bg-yellow-50 text-yellow-400 hover:bg-yellow-100'
  },
  {
    value: 'high',
    label: 'High',
    selected: 'bg-red-100 text-red-700 ring-2 ring-red-400',
    unselected: 'bg-red-50 text-red-400 hover:bg-red-100'
  },
  {
    value: 'urgent',
    label: 'Urgent',
    selected: 'bg-pink-100 text-pink-700 ring-2 ring-pink-400',
    unselected: 'bg-pink-50 text-pink-400 hover:bg-pink-100'
  }
]
</script>