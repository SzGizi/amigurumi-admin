<script setup>
import { ref, onMounted, watch } from 'vue'
import { toast } from 'vue3-toastify'
import { h } from 'vue'

const props = defineProps({
  modelType: String,
  modelId: Number,
  patternMainImageId: Number,
})

const emit = defineEmits(['updateDeletedImages', 'updateMainImageId'])

const endpointMap = {
  AmigurumiPattern: `/api/patterns/${props.modelId}/images`,
  AmigurumiSection: `/api/sections/${props.modelId}/images`,
}
const endpoint = endpointMap[props.modelType]

const images = ref([])
const pendingDeleteIds = ref([])
const mainImageId = ref(props.patternMainImageId || null)
const uploading = ref(false)

onMounted(async () => {
  if (!endpoint) {
    console.error('Unknown modelType:', props.modelType)
    return
  }

  try {
    const res = await fetch(endpoint)
    const data = await res.json()
    images.value = data

    // Frissítjük a mainImageId-t ha még nincs
    if (!mainImageId.value && data.length) {
      mainImageId.value = data[0].id
      emit('updateMainImageId', data[0].id)
    }
  } catch (err) {
    console.error('Failed to fetch images:', err)
  }
})

function onFileChange(event) {
  const files = event.target.files
  if (!files.length) return

  uploading.value = true

  Array.from(files).forEach(file => {
    const formData = new FormData()
    formData.append('image', file)
    formData.append('model_type', props.modelType)
    formData.append('model_id', props.modelId)

    fetch('/images/upload', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: formData,
    })
    .then(async res => {
      const data = await res.json()

      if (!res.ok) {
        toast.error(data.message || 'Upload failed')
        console.error('Server error response:', data)
        return
      }

      if (data.image && data.image.id && data.image.url) {
        images.value.push({ id: data.image.id, url: data.image.url })
      } else {
        console.error('Invalid server response structure:', data)
        toast.error('Upload failed: invalid server response')
      }
    })
    .catch(() => {
      toast.error('Upload error')
    })
    .finally(() => {
      uploading.value = false
    })
  })

  event.target.value = null
}

function removeImage(id) {
  toast(
    ({ closeToast }) => {
      const confirmDelete = () => {
        closeToast()
        pendingDeleteIds.value.push(id)
        images.value = images.value.filter(img => img.id !== id)
        emit('updateDeletedImages', pendingDeleteIds.value)
      }
      const cancelDelete = () => {
        closeToast()
      }

      return h('div', { style: 'padding:10px; max-width:250px;' }, [
        h('p', 'Are you sure you want to delete this image?'),
        h('div', { style: 'display:flex; justify-content:space-between;' }, [
          h('button', { class: 'btn btn-sm btn-danger', onClick: confirmDelete }, 'Yes'),
          h('button', { class: 'btn btn-sm btn-secondary', onClick: cancelDelete }, 'Cancel'),
        ])
      ])
    },
    { closeOnClick: false, closeButton: false, autoClose: false }
  )
}

function setMainImage(id) {
  mainImageId.value = id
  emit('updateMainImageId', id)
}
</script>

<template>
  <div>
    <input type="file" multiple @change="onFileChange" :disabled="uploading" />

    <div class="preview-main-buttons mt-3">
      <div v-for="img in images" :key="img.id" class="d-flex align-items-center mb-2">
        <img :src="img.url" alt="preview" style="max-width:100px; max-height:100px; object-fit:contain; margin-right:10px" />
        <button
          type="button"
          class="btn btn-sm"
          :class="mainImageId === img.id ? 'btn-primary' : 'btn-outline-primary'"
          @click="setMainImage(img.id)"
        >
          Set as Main
        </button>
        <button type="button" class="btn btn-sm btn-danger ms-2" @click="removeImage(img.id)">Delete</button>
      </div>
    </div>

    <input type="hidden" name="deleted_image_ids" :value="pendingDeleteIds.join(',')" />
    <input type="hidden" name="main_image_id" :value="mainImageId" />
  </div>
</template>
