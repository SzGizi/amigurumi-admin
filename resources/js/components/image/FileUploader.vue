<script setup>
import { ref, onMounted, defineExpose } from 'vue'
import { toast } from 'vue3-toastify'
import { h } from 'vue'
import { v4 as uuidv4 } from 'uuid'

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

const images = ref([]) // [{ id, url, isNew, file, uuid }]
const pendingDeleteIds = ref([])
const mainImageId = ref(props.patternMainImageId || null)
const uploading = ref(false)

onMounted(async () => {
  if (!endpoint) return console.error('Unknown modelType:', props.modelType)

  try {
    const res = await fetch(endpoint)
    const data = await res.json()
    images.value = data.map(img => ({ ...img, isNew: false }))

    if (!mainImageId.value && images.value.length) {
      mainImageId.value = images.value[0].id
      emit('updateMainImageId', images.value[0].id)
    }
  } catch (err) {
    console.error('Failed to fetch images:', err)
  }
})

function onFileChange(event) {
  const files = Array.from(event.target.files)
  if (!files.length) return

  files.forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      images.value.push({
        uuid: uuidv4(),
        isNew: true,
        file,
        url: e.target.result,
      })
    }
    reader.readAsDataURL(file)
  })

  event.target.value = null
}

function removeImage(img) {
  if (img.isNew) {
    toast(
      ({ closeToast }) => {
        const confirmDelete = () => {
          closeToast()
          // új, még nem feltöltött képet törlünk
          images.value = images.value.filter(i => i.uuid !== img.uuid)
        }
        const cancelDelete = () => closeToast()

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
    
  } else {
    // meglévő képet törlünk
    toast(
      ({ closeToast }) => {
        const confirmDelete = () => {
          closeToast()
          pendingDeleteIds.value.push(img.id)
          images.value = images.value.filter(i => i.id !== img.id)
          emit('updateDeletedImages', pendingDeleteIds.value)
        }
        const cancelDelete = () => closeToast()

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
}

function setMainImage(img) {
  if (img.isNew) {
    toast.warn('You can only set uploaded images as main.')
    return
  }
  mainImageId.value = img.id
  emit('updateMainImageId', img.id)
}

async function uploadPendingImages() {
  const newImages = images.value.filter(i => i.isNew)

  if (!newImages.length) return
  uploading.value = true

  for (const img of newImages) {
    const formData = new FormData()
    formData.append('image', img.file)
    formData.append('model_type', props.modelType)
    formData.append('model_id', props.modelId)

    try {
      const res = await fetch('/images/upload', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
      })

      const data = await res.json()

      if (res.ok && data.image?.id && data.image?.url) {
        // Frissítjük az új képet a szerver válaszával
        img.id = data.image.id
        img.url = data.image.url
        img.isNew = false
        delete img.file
        delete img.uuid

        if (!mainImageId.value) {
          mainImageId.value = data.image.id
          emit('updateMainImageId', data.image.id)
        }
      } else {
        toast.error(data.message || 'Upload failed')
        console.error('Upload failed:', data)
      }
    } catch (err) {
      toast.error('Upload error')
      console.error(err)
    }
  }

  uploading.value = false
}

defineExpose({
  uploadPendingImages,
})
</script>

<template>
  <div>
    <input type="file" multiple @change="onFileChange" :disabled="uploading" />

    <div class="preview-main-buttons mt-3">
      <div
        v-for="img in images"
        :key="img.id || img.uuid"
        class="d-flex align-items-center mb-2"
      >
        <img
          :src="img.url"
          alt="preview"
          style="max-width:100px; max-height:100px; object-fit:contain; margin-right:10px"
        />
        <button
          type="button"
          class="btn btn-sm"
          :class="mainImageId === img.id ? 'btn-primary' : 'btn-outline-primary'"
          @click="setMainImage(img)"
          :disabled="img.isNew"
        >
          Set as Main
        </button>
        <button type="button" class="btn btn-sm btn-danger ms-2" @click="removeImage(img)">
          Delete
        </button>
        <span v-if="img.isNew" class="text-muted ms-2">(New, must save before set Main)</span>
      </div>
    </div>

    <input type="hidden" name="deleted_image_ids" :value="pendingDeleteIds.join(',')" />
    <input type="hidden" name="main_image_id" :value="mainImageId" />
  </div>
</template>
