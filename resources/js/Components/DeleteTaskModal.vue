<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h4 class="font-bold mb-4 text-lg text-red-600">Delete Task</h4>
      <p class="mb-4">Are you sure you want to delete the task <span class="font-bold">"{{ task?.title }}"</span>?</p>
      <p class="mb-2 text-sm text-gray-500">Type the task name to confirm:</p>
      <input
        v-model="input"
        class="border rounded w-full px-2 py-1 mb-4"
        :placeholder="task?.title"
        @keyup.enter="tryDelete"
      />
      <div class="flex justify-end gap-2">
        <button @click="close" type="button" class="px-3 py-1 rounded bg-gray-200">Cancel</button>
        <button
          :disabled="input !== task?.title"
          @click="tryDelete"
          class="px-4 py-2 rounded bg-red-600 text-white disabled:opacity-50"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  task: Object,
})
const emit = defineEmits(['close', 'delete'])

const input = ref('')

watch(() => props.isOpen, (open) => {
  if (open) input.value = ''
})

const close = () => emit('close')
const tryDelete = () => {
  if (input.value === props.task?.title) emit('delete')
}
</script>