<script setup>
import vueFilePond from 'vue-filepond'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileRename from 'filepond-plugin-file-rename'
import { ref, onMounted } from 'vue'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'

const props = defineProps({
  modelType: String,
  modelId: Number,
})

const endpointMap = {
  AmigurumiPattern: `/api/patterns/${props.modelId}/images`,
  AmigurumiSection: `/api/sections/${props.modelId}/images`,
  
}
const endpoint = endpointMap[props.modelType]

const FilePond = vueFilePond(
  FilePondPluginImagePreview,
  FilePondPluginFileValidateType,
  FilePondPluginFileRename
)

const pondFiles = ref([]) // előtöltött fájlok

onMounted(async () => {
  
  if (!endpoint) {
    console.error('Unknown modelType:', props.modelType)
  } else {
    const res = await fetch(endpoint)
    const images = await res.json()

    

    pondFiles.value = images.map(image => ({
      source: image.url,
      options: {
        type: 'local',
        metadata: {
          url: image.url,
        }
      }
    }))
  }
})

const server = {
  fetch: loadImage,
  load: loadImage, // optional fallback
  process: (fieldName, file, metadata, load, error, progress, abort) => {
    const formData = new FormData()
    formData.append('image', file)
    formData.append('model_type', props.modelType)
    formData.append('model_id', props.modelId)

    const controller = new AbortController()

    fetch('/images/upload', {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      signal: controller.signal,
    })
      .then((res) => res.json())
      .then((res) => {
        if (res.image) {
          load(res.image.id)
        } else {
          error('Upload failed')
        }
      })
      .catch(() => {
        error('Upload error')
      })

    return {
      abort: () => controller.abort()
    }
  },
  revert: null
}
function loadImage(source, load, error, progress, abort, headers) {
  const controller = new AbortController()

  fetch(source, {
    method: 'GET',
    signal: controller.signal,
  })
    .then((res) => res.blob())
    .then(load)
    .catch(error)

  return {
    abort: () => controller.abort(),
  }
}
</script>

<template>
 <FilePond
 
  name="image"
  :files="pondFiles"
  :server="server"
  allow-multiple
  accepted-file-types="image/*"
  label-idle="Drag & Drop your image or Browse"
  @error="console.error('FilePond error:', $event)"

  />  
 


</template>
