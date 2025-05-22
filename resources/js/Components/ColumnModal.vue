<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white p-6 rounded shadow-lg w-80">
      <h4 class="font-bold mb-4">{{ column ? 'Edit column' : 'New Column' }}</h4>
      <input
        v-model="localTitle"
        class="border rounded w-full px-2 py-1 mb-4"
        placeholder="Column title"
        required
        @keyup.enter="submit"
        autofocus
      />
      <div class="flex justify-end gap-2">
        <button @click="close" class="px-3 py-1 rounded bg-gray-200">Cancel</button>
        <button @click="submit" class="px-3 py-1 rounded bg-blue-600 text-white">
          {{ column ? 'Save' : 'Create' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  isOpen: Boolean,
  column: Object
})
const emit = defineEmits(['close', 'submit'])

const localTitle = ref(props.column?.title || '')

watch(() => props.column, (val) => {
  localTitle.value = val?.title || ''
})

const close = () => emit('close')
const submit = () => {
  if (localTitle.value.trim()) {
    emit('submit', localTitle.value.trim())
    close()
  }
}
</script>