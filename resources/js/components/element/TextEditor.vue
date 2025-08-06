<!-- src/components/element/CkEditor.vue -->
<template>
  <div ref="editorContainer"></div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})
const emit = defineEmits(['update:modelValue'])

const editorContainer = ref(null)
let editorInstance = null

onMounted(() => {
  ClassicEditor
    .create(editorContainer.value, {
      toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo'],
    })
    .then(editor => {
      editorInstance = editor
      editor.setData(props.modelValue || '')

      editor.model.document.on('change:data', () => {
        emit('update:modelValue', editor.getData())
      })
    })
    .catch(error => {
      console.error('CKEditor init error:', error)
    })
})

onBeforeUnmount(() => {
  if (editorInstance) {
    editorInstance.destroy()
    editorInstance = null
  }
})

watch(() => props.modelValue, (newValue) => {
  if (editorInstance && editorInstance.getData() !== newValue) {
    editorInstance.setData(newValue || '')
  }
})
</script>
