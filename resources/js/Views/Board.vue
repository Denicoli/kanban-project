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
              :id="column.id"
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
              @edit-task="onEditTask"
              @delete-task="onDeleteTask"
              @delete-column="onDeleteColumn"
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
        :task="selectedTask"
        @close="showTaskModal = false"
        @submit="handleTaskSubmit"
      />
      <DeleteTaskModal 
        :isOpen="showDeleteTaskModal"
        :task="taskToDelete"
        @close="showDeleteTaskModal = false"
        @delete="confirmDeleteTask"
      />
      <ColumnModal
        :isOpen="showColumnModal"
        @close="showColumnModal = false"
        @submit="handleCreateColumn"
      />
      <DeleteColumnModal
        :is-open="showDeleteColumnModal"
        :column="columnToDelete"
        @close="showDeleteColumnModal = false"
        @delete="confirmDeleteColumn"
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
import DeleteTaskModal from '@/Components/DeleteTaskModal.vue'
import DeleteColumnModal from '@/Components/DeleteColumnModal.vue'

const route = useRoute()
const board = ref(null)
const columns = ref([])

const showTaskModal = ref(false)
const selectedTask = ref(null)
const showDeleteTaskModal = ref(false)
const taskToDelete = ref(null)

const selectedColumn = ref(null)
const showColumnModal = ref(false)
const openMenuColumnId = ref(null)
const showDeleteColumnModal = ref(false)
const columnToDelete = ref(null)

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

const createTask = async (taskData) => {
  if (!selectedColumn.value) return

  const newTask = {
    ...taskData,
    column_id: selectedColumn.value.id,
    order: selectedColumn.value.tasks.length,
  }

  const { data } = await api.post(`/columns/${selectedColumn.value.id}/tasks`, newTask)
  selectedColumn.value.tasks.push(data)
  showTaskModal.value = false
}

const updateTask = async (taskData) => {
  const updatedTask = {
    ...selectedTask.value,
    ...taskData,
    column_id: selectedTask.value.column_id,
  }

  const { data } = await api.put(`/tasks/${selectedTask.value.id}`, updatedTask)  

  const col = columns.value.find(col => col.id === data.column_id)
  if (col) {
    const taskIndex = col.tasks.findIndex(t => t.id === data.id)
    if (taskIndex !== -1) {
      col.tasks = [
        ...col.tasks.slice(0, taskIndex),
        data,
        ...col.tasks.slice(taskIndex + 1)
      ]
    }
  }
  showTaskModal.value = false
}

const addTaskToColumn = (column) => {
  selectedTask.value = null
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

const onEditTask = (task) => {
  selectedTask.value = task
  selectedColumn.value = columns.value.find(col => col.id === task.column_id)
  if (!selectedColumn.value) {
    selectedColumn.value = columns.value.find(col => col.tasks.some(t => t.id === task.id));
  }
  showTaskModal.value = true
}

const onDeleteTask = (task) => {
  taskToDelete.value = task
  showDeleteTaskModal.value = true
}

const confirmDeleteTask = async () => {
  if (!taskToDelete.value) return

  const col = columns.value.find(col => col.id === taskToDelete.value.column_id)
  
  if (col) {
    col.tasks = col.tasks.filter(t => t.id !== taskToDelete.value.id)
  }

  await api.delete(`/tasks/${taskToDelete.value.id}`)
  showDeleteTaskModal.value = false
  taskToDelete.value = null
}

const onDeleteColumn = (column) => {
  columnToDelete.value = column
  showDeleteColumnModal.value = true
}

const confirmDeleteColumn = async () => {
  if (!columnToDelete.value) return

  if (columnToDelete.value.tasks.length > 0) return

  await api.delete(`/columns/${columnToDelete.value.id}`)

  columns.value = columns.value.filter(col => col.id !== columnToDelete.value.id)

  showDeleteColumnModal.value = false
  columnToDelete.value = null
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
  if (selectedTask.value) {
    await updateTask(taskData)
  } else {
    await createTask(taskData)
  }
  selectedTask.value = null
  showTaskModal.value = false
  selectedColumn.value = null
}

const handleMoveTask = (evt, toColumn) => {
  if (evt.added) {
    const task = evt.added.element
    if (!task.id) return
    task.column_id = toColumn.id
  }

  for (let i = 0; i < toColumn.tasks.length; i++) {
    const t = toColumn.tasks[i]
    t.order = i
    api.put(`/tasks/${t.id}`, {
      column_id: toColumn.id,
      order: t.order,
      title: t.title
    })
  }
}

const generateTestTasks = async () => {
  const testTaskExists = columns.value.some(col =>
    col.tasks.some(task => task.title && task.title.startsWith('Test Task'))
  )
  if (testTaskExists) return

  if (!columns.value.length) return
  let statuses = ['not-started', 'in-research', 'on-track', 'code-review', 'completed']
  let priorities = ['low', 'medium', 'high', 'urgent']
  const today = new Date()
  for (let i = 0; i < 20; i++) {
    const colIdx = i % columns.value.length
    const column = columns.value[colIdx]

    const dueDate = new Date(today)
    dueDate.setDate(today.getDate() + (i % 8))
    const yyyy = dueDate.getFullYear()
    const mm = String(dueDate.getMonth() + 1).padStart(2, '0')
    const dd = String(dueDate.getDate()).padStart(2, '0')
    const due_date = `${yyyy}-${mm}-${dd}`

    const newTask = {
      title: `Test Task ${i + 1}`,
      description: `This is a test task #${i + 1}`,
      due_date,
      status: statuses[i % statuses.length],
      priority: priorities[i % priorities.length],
      order: column.tasks.length,
      column_id: column.id,
    }
    const { data } = await api.post(`/columns/${column.id}/tasks`, newTask)
    column.tasks.push(data)
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
  // generateTestTasks()
})
</script>