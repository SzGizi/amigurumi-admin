<script setup>
import { ref, onMounted, defineExpose } from 'vue'
import { toast } from 'vue3-toastify'
import { h } from 'vue'
import { v4 as uuidv4 } from 'uuid'
import draggable from 'vuedraggable';


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
const fileInput = ref(null)


const triggerFileInput = () => {
  fileInput.value?.click()
}

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

  mainImageId.value = img.id
  emit('updateMainImageId', img.id)
}
async function updateImagesOrders() {
  images.value.forEach((img, index) => {
    img.order = index + 1
  })

  const reordered = images.value
    .filter(img => !img.isNew) // csak a meglévő képeket küldjük
    .map(img => ({
      id: img.id,
      order: img.order
    }))

  try {
    await fetch('/images/reorder', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({ images: reordered }),
    })
  } catch (err) {
    toast.error('Failed to update image order')
    console.error('Reorder error:', err)
  }
}


async function uploadPendingImages() {
  const newImages = images.value.filter(i => i.isNew)

  if (!newImages.length) return
  uploading.value = true

  for (const img of newImages) {
    const formData = new FormData();
    formData.append('image', img.file);
    formData.append('order', img.order ?  img.order  : newImages.length);
    formData.append('is_main', img.id === mainImageId.value ? 1 : 0);
    formData.append('model_type', props.modelType);
    formData.append('model_id', props.modelId);

    try {
      const res = await fetch('/images/upload', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
      })

      const data = await res.json();

      if (res.ok && data.image?.id && data.image?.url) {
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
        toast.error(data.message || 'Upload failed');
        console.error('Upload failed:', data);
      }
    } catch (err) {
      toast.error('Upload error');
      console.error(err);
    }
  }

  uploading.value = false;
}

defineExpose({
  uploadPendingImages,
})
</script>


<template>
  <div class="dropzone" @click="triggerFileInput">
    <p>Drag image here or</p>
    <button type="button">Click to upload</button>
    <input type="file" ref="fileInput" multiple @change="onFileChange" :disabled="uploading" />
  </div>

    <draggable
    v-model="images"
    handle=".drag-handle"
    animation="150"
    item-key="img => img.id || img.uuid"
    ref="draggableImages"
    tag="div"
    @update="updateImagesOrders"
    class="fileUploaderContainer  row align-items-center"
    
    >
      <template  #item="{ element: img }">
        <div class="fileUploaderItem col-md-2 col-6 mb-2">
          <img :src="img.url" alt="preview" class="fileUploaderImage" />

          <div class="fileUploaderActionsContainer">
            <span class="drag-handle btn btn-sm" title="Set order">⠿</span>

            <button
              type="button"
              class="btn btn-sm ms-1"
              :class="mainImageId === img.id ? 'btn-primary' : 'btn-dark'"
              @click="setMainImage(img)"
              title="Set as main image"
            >
              <i class="bi bi-layer-forward"></i>
            </button>

            <button
              type="button"
              title="Delete image"
              class="btn btn-sm btn-danger ms-1"
              @click="removeImage(img)"
            >
              <i class="bi bi-x"></i>
            </button>
          </div>
        </div>
      </template>
    </draggable>


  

  <input type="hidden" name="deleted_image_ids" :value="pendingDeleteIds.join(',')" />
  <input type="hidden" name="main_image_id" :value="mainImageId" />
</template>
