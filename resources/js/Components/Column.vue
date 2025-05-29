<template>
  <div class="flex flex-col items-center min-w-[16rem] max-w-xs w-full sm:max-w-sm md:max-w-md lg:max-w-xl xl:max-w-xl">
    <div class="flex items-center justify-between w-full mb-2 ml-2">
      <div class="flex items-center gap-1">
        <span v-if="!editing" class="font-bold cursor-pointer" @click="startEdit">
          {{ title }}
        </span>
        <input
          v-else
          ref="inputRef"
          v-model="newTitle"
          @blur="submitEdit"
          @keyup.enter="submitEdit"
          class="border rounded-md px-1 py-0.5 text-sm font-bold"
          :style="{ width: Math.max(newTitle.length * 0.75, 6) + 'ch' }"
        />
        <button v-if="!editing" @click="startEdit" class="text-gray-400 hover:text-blue-600">
          <PencilIcon class="w-4 h-4" />
        </button>
      </div>
      <div class="relative">
        <button @click="emit('toggle-menu')" class="p-1 rounded hover:bg-gray-200">
          <EllipsisVerticalIcon class="w-4 h-4" />
        </button>
        <div
          v-if="menuOpen"
          ref="menuRef"
          class="absolute right-0 mt-2 w-32 bg-white shadow-lg rounded z-10"
        >
          <button @mousedown.stop="emitDelete" class="block w-full text-left px-4 py-2 text-red-500 hover:bg-gray-100">
            Delete
          </button>
        </div>
      </div>
    </div>
    <draggable
      :list="tasks"
      group="tasks"
      item-key="id"
      class="bg-white shadow p-4 rounded-lg w-full flex-1 overflow-y-auto custom-scrollbar"
      style="max-height: 400px; min-height: 100px;"
      @change="onMoveTask"
    >
      <template #item="{ element: task }">
        <Task 
          :task="task"
          @edit="emit('edit-task', task)"
          @delete="emit('delete-task', task)"
        />
      </template>
    </draggable>
    <button 
      @click="emitAddTask" 
      class="flex items-center gap-1 mt-2 text-blue-600 hover:text-blue-800 font-medium"
    >
      <PlusIcon class="w-5 h-5" />
      <span class="select-none">Add Task</span>
    </button>
  </div>
</template>

<script setup>
import draggable from 'vuedraggable'
import { ref, watch, nextTick, onBeforeUnmount } from 'vue'
import Task from './Task.vue'

const props = defineProps({
  title: String,
  tasks: { type: Array, default: () => [] },
  menuOpen: Boolean
})
const emit = defineEmits(['edit', 'delete', 'add-task', 'add-column', 'move-task', 'edit-task', 'delete-task', 'toggle-menu', 'close-menu'])

const editing = ref(false)
const newTitle = ref(props.title)
const inputRef = ref(null)
const menuRef = ref(null)

const emitDelete = () => {
  emit('delete')
  emit('close-menu')
}

const emitAddTask = () => {
  emit('add-task')
}

const onMoveTask = (evt) => {
  emit('move-task', evt)
}

const startEdit = () => {
  editing.value = true
  newTitle.value = props.title
  nextTick(() => {
    if (inputRef.value) inputRef.value.focus()
  })
}

const submitEdit = () => {
  if (newTitle.value.trim() && newTitle.value !== props.title) {
    emit('edit', newTitle.value.trim())
  }
  editing.value = false
}

const cancelEdit = () => {
  editing.value = false
  newTitle.value = props.title
}

const handleClickOutsideEdit = (event) => {
  if (editing.value && inputRef.value && !inputRef.value.contains(event.target)) {
    cancelEdit()
  }
}

const handleClickOutsideMenu = (event) => {
  if (props.menuOpen && menuRef.value && !menuRef.value.contains(event.target)) {
    emit('close-menu')
  }
}

watch(editing, (val) => {
  if (val) document.addEventListener('mousedown', handleClickOutsideEdit)
  else document.removeEventListener('mousedown', handleClickOutsideEdit)
})
watch(() => props.menuOpen, (open) => {
  if (open) document.addEventListener('mousedown', handleClickOutsideMenu)
  else document.removeEventListener('mousedown', handleClickOutsideMenu)
})
onBeforeUnmount(() => {
  document.removeEventListener('mousedown', handleClickOutsideEdit)
})

watch(() => props.title, (val) => {
  newTitle.value = val
})
</script>