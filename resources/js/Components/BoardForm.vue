<template>
  <form @submit.prevent="submitForm">
    <div class="mb-4">
      <label for="name" class="label">Name</label>
      <input type="text" v-model="form.name" required class="input" />
    </div>
    <div class="mb-6">
      <label for="description" class="label">Description</label>
      <input type="text" v-model="form.description" class="input" />
    </div>
    <div class="flex justify-end space-x-3">
      <button
        type="button"
        @click="$emit('cancel')"
        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 transition"
      >
        Cancel
      </button>
      <button
        type="submit"
        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition"
      >
        Save
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive, watch } from 'vue'

const form = reactive({
  name: '',
  description: '',
})

const emit = defineEmits(['submit', 'cancel'])
const props = defineProps({
  name: { type: String, required: true },
  description: { type: String, required: false, default: '' }
})

watch(
  () => [props.name, props.description],
  ([newName, newDescription]) => {
    form.name = newName
    form.description = newDescription
  },
  { immediate: true }
)

const resetForm = () => {
  form.name = ''
  form.description = ''
}

const submitForm = () => {
  if (!form.name.trim()) return
  emit('submit', { ...form })
  resetForm()
}
</script>

<style scoped>
.input {
  @apply w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none;
}

.label {
  @apply block text-sm font-medium mb-1 text-gray-700;
}
</style>
