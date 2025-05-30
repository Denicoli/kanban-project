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
          @keyup.esc="cancelEdit"
          class="border rounded-md px-2 py-1 text-sm font-bold bg-white shadow focus:outline-none focus:ring-2 focus:ring-blue-400 transition w-[220px] min-w-[180px] max-w-[320px]"
        />
        <button v-if="!editing" @click="startEdit" class="text-gray-400 hover:text-blue-600">
          <PencilIcon class="w-4 h-4" />
        </button>
      </div>
      <div>
        <button
          :disabled="tasks.length > 0"
          @click.stop="handleDeleteColumn"
          class="p-1 rounded hover:bg-gray-200"
          :class="tasks.length > 0 ? 'text-gray-400 cursor-not-allowed' : 'text-red-600'"
          :title="tasks.length > 0 ? 'Cannot delete a column with tasks' : 'Delete column'"
        >
          <TrashIcon class="w-4 h-4" />
        </button>
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
import { ref, watch, nextTick, computed } from 'vue'
import Task from './Task.vue'

const props = defineProps({
  title: String,
  tasks: { type: Array, default: () => [] },
  id: [String, Number]
})
const emit = defineEmits(['edit', 'delete', 'add-task', 'add-column', 'move-task', 'edit-task', 'delete-task', 'delete-column'])

const editing = ref(false)
const newTitle = ref(props.title)
const inputRef = ref(null)

const columnData = computed(() => ({
  id: props.id,
  title: props.title,
  tasks: props.tasks
}))

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

const handleDeleteColumn = () => {
  if (props.tasks.length > 0) return
  emit('delete-column', columnData.value)
}

watch(() => props.title, (val) => {
  newTitle.value = val
})
</script>