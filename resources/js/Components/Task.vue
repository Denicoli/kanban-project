<template>
  <div 
    class="bg-white rounded-xl shadow p-4 mb-4 border border-gray-100 hover:shadow-lg transition relative"
    @click.stop="emitEdit"
  >
    <div class="flex items-center justify-between mb-1">
      <span
        class="text-xs font-semibold px-2 py-0.5 rounded-full"
        :class="statusClass"
      >
        {{ statusLabel }}
      </span>
      <div class="relative" ref="menuRef">
        <button @click.stop="toggleMenu" class="p-1 rounded hover:bg-gray-200">
          <EllipsisIcon class="w-4 h-4" />
        </button>
        <div
          v-if="menuOpen"
          class="absolute right-0 mt-2 w-28 bg-white shadow-lg rounded z-20"
        >
          <button @mousedown.stop="emitEdit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">Edit</button>
          <button @mousedown.stop="emitDelete" class="block w-full text-left px-4 py-2 text-red-500 hover:bg-gray-100">Delete</button>
        </div>
      </div>
    </div>
    <div class="font-semibold text-base mb-1 truncate">{{ task.title }}</div>
    <div class="text-xs text-gray-500 mb-2 truncate">{{ task.description }}</div>
    <div class="flex items-center justify-between mt-4">
      <div v-if="task.due_date" class="flex items-center text-xs text-gray-400">
        <CalendarIcon class="w-4 h-4 mr-1" />
        {{ dueDateFormatted }}
      </div>
      <span
        class="ml-2 text-xs font-semibold px-2 py-0.5 rounded-full"
        :class="priorityBadgeClass"
      >
        {{ priorityLabel }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, onBeforeUnmount, computed } from 'vue'

const props = defineProps({
  task: {
    type: Object,
    required: true
  }
})
const emit = defineEmits(['edit', 'delete'])

const menuOpen = ref(false)
const menuRef = ref(null)

function toggleMenu() {
  menuOpen.value = !menuOpen.value
}
function emitEdit() {
  emit('edit', props.task)
  menuOpen.value = false
}
function emitDelete() {
  emit('delete', props.task)
  menuOpen.value = false
}

function handleClickOutside(event) {
  if (menuOpen.value && menuRef.value && !menuRef.value.contains(event.target)) {
    menuOpen.value = false
  }
}
document.addEventListener('mousedown', handleClickOutside)
onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})

const dueDateFormatted = computed(() => {
  if (!props.task.due_date) return ''
  const [year, month, day] = props.task.due_date.split('-')
  return `${month}/${day}/${year}`
})

// Map backend status to label and color
const statusMap = {
  'not-started': { label: 'Not Started', color: 'bg-purple-100 text-purple-700' },
  'in-research': { label: 'In Research', color: 'bg-yellow-100 text-yellow-700' },
  'on-track': { label: 'On Track', color: 'bg-pink-100 text-pink-700' },
  'code-review': { label: 'Code Review', color: 'bg-blue-100 text-blue-700' },
  'completed': { label: 'Complete', color: 'bg-green-100 text-green-700' }
}
const statusLabel = computed(() => {
  const status = props.task.status || 'not-started'
  return statusMap[status]?.label || 'Not Started'
})
const statusClass = computed(() => {
  const status = props.task.status || 'not-started'
  return statusMap[status]?.color || 'bg-purple-100 text-purple-700'
})

const priorityMap = {
  'low':    { label: 'Low',    badge: 'bg-blue-100 text-blue-700' },
  'medium': { label: 'Medium', badge: 'bg-yellow-100 text-yellow-700' },
  'high':   { label: 'High',   badge: 'bg-red-100 text-red-700' },
  'urgent': { label: 'Urgent', badge: 'bg-pink-100 text-pink-700' }
}
const priorityLabel = computed(() => {
  const priority = props.task.priority || 'low'
  return priorityMap[priority]?.label || 'Low'
})
const priorityBadgeClass = computed(() => {
  const priority = props.task.priority || 'low'
  return priorityMap[priority]?.badge || 'bg-blue-100 text-blue-700'
})
</script>