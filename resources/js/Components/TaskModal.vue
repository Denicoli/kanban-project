<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white p-6 rounded shadow-lg w-80">
      <h4 class="font-bold mb-4">{{ task ? 'Edit Task' : 'New Task' }}</h4>
      <form @submit.prevent="submit">
        <input
          v-model="form.title"
          class="border rounded w-full px-2 py-1 mb-4"
          placeholder="Task title"
          autofocus
        />

        <textarea
          v-model="form.description"
          class="border rounded w-full px-2 py-1 mb-4"
          placeholder="Task description"
          rows="2"
        />

        <label class="block text-sm font-medium mb-1">Due Date</label>
        <input
          v-model="form.due_date"
          type="date"
          class="border rounded w-full px-2 py-1 mb-4"
          ref="dueDateInput"
          :min="today"
          @mousedown.prevent="openDatePicker"
        />

        <label class="block text-sm font-medium mb-1">Status</label>
        <select v-model="form.status" class="w-full border rounded px-2 py-1 mb-3">
          <option value="not-started">Not Started</option>
          <option value="in-research">In Research</option>
          <option value="on-track">On Track</option>
          <option value="code-review">Code Review</option>
          <option value="completed">Complete</option>
        </select>

        <label class="block text-sm font-medium mb-1">Priority</label>
        <select v-model="form.priority" class="w-full border rounded px-2 py-1 mb-3">
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
          <option value="urgent">Urgent</option>
        </select>

        <div class="flex justify-end gap-2">
          <button @click="close" type="button" class="px-3 py-1 rounded bg-gray-200">Cancel</button>
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            {{ task ? 'Save' : 'Create' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const dueDateInput = ref(null)

const props = defineProps({
  isOpen: Boolean,
  task: Object
})
const emit = defineEmits(['close', 'submit'])

const form = ref({
  title: props.task?.title || '',
  description: props.task?.description || '',
  due_date: props.task?.due_date || '',
  status: props.task?.status || 'not-started',
  priority: props.task?.priority || 'low'
})

watch(() => props.task, (val) => {
  form.value.title = val?.title || ''
  form.value.description = val?.description || ''
  form.value.due_date = val?.due_date || ''
  form.value.status = val?.status || 'not-started'
  form.value.priority = val?.priority || 'low'
})

const today = computed(() => {
  const date = new Date()
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
})

const close = () => emit('close')
const submit = () => {
  if (form.value.title.trim()) {
    emit('submit', { ...form.value })
    close()
  }
}
const openDatePicker = () => {
  if (dueDateInput.value) {
    dueDateInput.value.showPicker?.()
    dueDateInput.value.focus()
    dueDateInput.value.click()
  }
}
</script>