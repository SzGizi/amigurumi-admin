<script setup>
import vueFilePond from 'vue-filepond'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileRename from 'filepond-plugin-file-rename'
import { ref, onMounted } from 'vue'
import { h } from 'vue'
import { toast } from 'vue3-toastify'


import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css'

import { defineEmits } from 'vue';

const emit = defineEmits(['updateDeletedImages']);

const props = defineProps({
  modelType: String,
  modelId: Number,
  name: String
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
const pendingDeleteIds = ref([]) // törlésre váró fájlok

onMounted(async () => {
  
  if (!endpoint) {
    console.error('Unknown modelType:', props.modelType)
  } else {
    const res = await fetch(endpoint)
    const images = await res.json()

    
    
    pondFiles.value = images.map(image => {
  
      return {
        source: image.url,
        options: {
          type: 'local',
          metadata: {
            id: image.id,
            url: image.url,
          }
        }
      }
    })
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
  remove: (source, load, error) => {
    toast(
      ({ closeToast }) => {
        const confirm = () => {
          closeToast()
          // Törlés megerősítve, itt jelöljük a törlendő képet
          const fileToDelete = pondFiles.value.find(file => file.source === source)
          if (fileToDelete) {
            pendingDeleteIds.value.push(fileToDelete.options.metadata.id)
            emit('updateDeletedImages', pendingDeleteIds.value); // Esemény küldése
          } else {
            console.warn('Remove: file not found for source', source)
          }
          load() // jelezzük FilePond-nak, hogy törlés kész
        }
        const cancel = () => {
          closeToast()
          error('User cancelled deletion') // jelezzük a FilePond-nak, hogy törlés megszakítva
        }

        return h('div', { style: 'padding: 10px; max-width: 250px;' }, [
          h('p', 'Are you sure you want to delete this image?'),
          h('div', { style: 'display: flex; justify-content: space-between;' }, [
            h('button', { onClick: confirm, class: 'btn btn-sm btn-danger' }, 'Yes'),
            h('button', { onClick: cancel, class: 'btn btn-sm btn-secondary' }, 'Cancel'),
          ])
        ])
      },
      {
        closeOnClick: false,
        closeButton: false,
        autoClose: false,
      }
    )
  }
  
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
function onDelete(id) {
  pendingDeleteIds.value.push(id);
  emit('updateDeletedImages', pendingDeleteIds.value);
}

</script>

<template>
 <FilePond
    name="images"
    :files="pondFiles"
    allow-multiple
    :server="server"
    :allowRevert="false"
   
    
  
  />  
    <input
    type="hidden"
    name="deleted_image_ids"
    :value="pendingDeleteIds.join(',')"
  />
 


</template>
