<template>
  <div class="p-0 min-h-screen bg-gray-50">
    <AppHeader :title="board?.name || 'Board'" :showBack="true" />
    <div class="p-6">
      <div 
        class="w-full min-w-0 max-w-full flex gap-4 items-start overflow-x-auto pb-4 custom-scrollbar"
        style="min-height: 80vh;"
      >
        <draggable 
          v-model="columns"
          group="columns"
          item-key="id"
          class="flex gap-4 items-start flex-nowrap"
          @end="onColumnsReorder"
        >
          <template #item="{ element: column, index: idx }">
            <Column
              :key="column.id"
              :title="column.title"
              :tasks="column.tasks"
              :menu-open="openMenuColumnId === column.id"
              @toggle-menu="() => openMenuColumnId = openMenuColumnId === column.id ? null : column.id"
              @close-menu="openMenuColumnId = null"
              @edit="newTitle => editColumn(column, newTitle)"
              @delete="() => deleteColumn(column)"
              @add-task="() => addTaskToColumn(column)"
              @add-column="() => addColumnAfter(idx)"
              @move-task="evt => handleMoveTask(evt, column)"
            />
          </template>
        </draggable>
        <button
          @click="showColumnModal = true"
          class="flex items-center gap-1 h-10 mt-0 self-start text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap"
        >
          <PlusIcon class="w-5 h-5" />
          <span class="select-none">Add Column</span>
        </button>
      </div>
      <TaskModal
        :isOpen="showTaskModal"
        @close="showTaskModal = false"
        @submit="handleTaskSubmit"
      />
      <ColumnModal
        :isOpen="showColumnModal"
        @close="showColumnModal = false"
        @submit="handleCreateColumn"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import draggable from 'vuedraggable'
import api from '../axios'
import AppHeader from '@/Components/AppHeader.vue'
import Column from '@/Components/Column.vue'
import TaskModal from '@/Components/TaskModal.vue'
import ColumnModal from '@/Components/ColumnModal.vue'

const route = useRoute()
const board = ref(null)
const columns = ref([])

const showTaskModal = ref(false)
const selectedColumn = ref(null)
const showColumnModal = ref(false)
const openMenuColumnId = ref(null)

const editColumn = async (column, newTitle) => {
  column.title = newTitle
  await api.put(`/columns/${column.id}`, { title: newTitle })
}

const deleteColumn = async (column) => {
  const idx = columns.value.findIndex(c => c.id === column.id)
  if (idx !== -1) columns.value.splice(idx, 1)
  await api.delete(`/columns/${column.id}`)
}

const addColumnAfter = async (idx) => {
  const newColumn = {
    title: 'New Column',
    order: columns.value.length,
  }
  const { data } = await api.post(`/boards/${board.value.id}/columns`, newColumn)
  columns.value.splice(idx + 1, 0, { ...data, tasks: [] })
}

const addTaskToColumn = (column) => {
  selectedColumn.value = column
  showTaskModal.value = true
}

const onColumnsReorder = async (evt) => {
  let { oldIndex, newIndex } = evt
  if (oldIndex === newIndex) return
  
  const updatePromises = columns.value.map((col, i) =>
    api.put(`/columns/${col.id}`, { 
      title: col.title,
      order: i 
    })
  )
  await Promise.all(updatePromises)
}

const handleCreateColumn = async (title) => {
  if (!title) return
  const newColumn = {
    title,
    order: columns.value.length,
  }
  const { data } = await api.post(`/boards/${board.value.id}/columns`, newColumn)
  columns.value.push({ ...data, tasks: [] })
  showColumnModal.value = false
}

const handleTaskSubmit = async (taskData) => {
  const newTask = {
    title: taskData.title,
    description: taskData.description,
    status: taskData.status,
    priority: taskData.priority,
    due_date: taskData.due_date,
    order: selectedColumn.value.tasks.length,
    column_id: selectedColumn.value.id,
  }
  const { data } = await api.post(`/columns/${selectedColumn.value.id}/tasks`, newTask)
  selectedColumn.value.tasks.push(data)
  showTaskModal.value = false
  selectedColumn.value = null
}

const handleMoveTask = (evt, toColumn) => {
  if (evt.added) {
    const task = evt.added.element
    if (!task.id) return
    task.column_id = toColumn.id
    task.order = evt.added.newIndex
    api.put(`/tasks/${task.id}`, { 
      column_id: toColumn.id,
      order: evt.added.newIndex,
      title: task.title
    })
  }
}

onMounted(async () => {
  const { id } = route.params
  const { data } = await api.get(`/boards/${id}`)
  board.value = data
  columns.value = data.columns
    .slice()
    .sort((a, b) => a.order - b.order)
    .map(col => ({
      ...col,
      tasks: col.tasks || []
    }))
})
</script>