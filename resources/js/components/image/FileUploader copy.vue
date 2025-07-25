<script setup>
import { ref, onMounted, defineExpose } from 'vue'
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

const images = ref([])               // Már feltöltött képek {id, url}
const newImages = ref([])            // Új, feltöltésre váró fájlok (File objektumok)
const previewUrls = ref([])          // Új képek preview URL-jei
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

    // Ha nincs mainImageId beállítva, akkor az első legyen
    if (!mainImageId.value && data.length) {
      mainImageId.value = data[0].id
      emit('updateMainImageId', data[0].id)
    }
  } catch (err) {
    console.error('Failed to fetch images:', err)
  }
})

function onFileChange(event) {
  const files = Array.from(event.target.files)
  if (!files.length) return

  files.forEach(file => {
    newImages.value.push(file)

    const reader = new FileReader()
    reader.onload = (e) => {
      previewUrls.value.push(e.target.result)
    }
    reader.readAsDataURL(file)
  })

  event.target.value = null
}

/**
 * Feltölti az újonnan kiválasztott képeket.
 * Ezt a külső form submit eseményén kell meghívni.
 */
async function uploadPendingImages() {
  if (!newImages.value.length) return

  uploading.value = true

  for (let i = 0; i < newImages.value.length; i++) {
    const file = newImages.value[i]
    const formData = new FormData()
    formData.append('image', file)
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

      if (res.ok && data.image && data.image.id && data.image.url) {
        images.value.push({ id: data.image.id, url: data.image.url })

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

  newImages.value = []
  previewUrls.value = []
  uploading.value = false
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

defineExpose({
  uploadPendingImages,
})
</script>

<template>
  <div>
    <input type="file" multiple @change="onFileChange" :disabled="uploading" />

    <div class="preview-main-buttons mt-3">
      <!-- Meglévő képek -->
      <div v-for="img in images" :key="'img-' + img.id" class="d-flex align-items-center mb-2">
        <img
          :src="img.url"
          alt="preview"
          style="max-width:100px; max-height:100px; object-fit:contain; margin-right:10px"
        />
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

      <!-- Új képek előnézete -->
      <div v-for="(url, index) in previewUrls" :key="'preview-' + index" class="d-flex align-items-center mb-2">
        <img
          :src="url"
          alt="new preview"
          style="max-width:100px; max-height:100px; object-fit:contain; margin-right:10px"
        />
        <span class="text-muted ms-2">(New)</span>
      </div>
    </div>

    <input type="hidden" name="deleted_image_ids" :value="pendingDeleteIds.join(',')" />
    <input type="hidden" name="main_image_id" :value="mainImageId" />
  </div>
</template>
