<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white p-6 rounded shadow-lg w-96">
      <h4 class="font-bold mb-4">{{ board ? 'Edit Board' : 'New Board' }}</h4>
      <input
        v-model="localName"
        class="border rounded w-full px-2 py-1 mb-4"
        placeholder="Board name"
        required
        autofocus
      />
      <textarea
        v-model="localDescription"
        class="border rounded w-full px-2 py-1 mb-4"
        placeholder="Board description"
        rows="2"
      />
      <div class="flex justify-end gap-2">
        <button @click="close" class="px-3 py-1 rounded bg-gray-200">Cancel</button>
        <button @click="submit" class="px-3 py-1 rounded bg-blue-600 text-white">
          {{ board ? 'Save' : 'Create' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
const props = defineProps({ isOpen: Boolean, board: Object })
const emit = defineEmits(['close', 'submit'])

const localName = ref(props.board?.name || '')
const localDescription = ref(props.board?.description || '')

watch(() => props.board, (val) => {
  localName.value = val?.name || ''
  localDescription.value = val?.description || ''
})

const close = () => emit('close')
const submit = () => {
  if (!localName.value.trim()) return
  emit('submit', {
    name: localName.value.trim(),
    description: localDescription.value.trim(),
    id: props.board?.id
  })
  close()
}
</script>