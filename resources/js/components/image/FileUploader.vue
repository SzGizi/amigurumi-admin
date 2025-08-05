<script setup>
import { ref, onMounted, defineExpose, computed  } from 'vue'
import { toast } from 'vue3-toastify'
import { h } from 'vue'
import { v4 as uuidv4 } from 'uuid'
import draggable from 'vuedraggable';
import { Cropper } from 'vue-advanced-cropper'


const props = defineProps({
  modelType: String,
  modelId: Number,
  modelUid: String,
  getSectionIdByUid: Function, 
  getAssemblyStepIdByUid: Function, 
  patternMainImageId: Number,
  hasMainImage: {
    type: Boolean,
    default: true  // alapból van main image, csak sectionnál false
  }
})

const emit = defineEmits(['updateDeletedImages', 'updateMainImageId'])

const endpointMap = {
  AmigurumiPattern: `/api/patterns/${props.modelId}/images`,
  AmigurumiSection: `/api/sections/${props.modelId}/images`,
  AmigurumiPatternAssemblyStep: `/api/assemblystep/${props.modelId}/images`,
}
const endpoint = endpointMap[props.modelType]

const images = ref([]) // [{ id, url, isNew, file, uuid }]
const pendingDeleteIds = ref([])
const mainImageId = ref(props.hasMainImage ? props.patternMainImageId || null : null)
const uploading = ref(false)
const fileInput = ref(null)


const triggerFileInput = () => {
  fileInput.value?.click()
}

const captionModalVisible = ref(false)
const currentCaptionImage = ref(null)
const captionDraft = ref('')

const croppingImage = ref(null)
const cropModalVisible = ref(false)
const cropperRef = ref(null)
const selectedAspectRatio = ref(null)
const mainImageUuid = ref(null);



onMounted(async () => {
  
  if(props.modelId == null){
    return ;
  } else {
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
  }
  
})

function onFileChange(event) {
  const files = Array.from(event.target.files)
  
  if (!files.length) return

  console.log('Selected files:', files);
  console.log('Current images:', images.value);
  

  files.forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      images.value.push({
        uuid: uuidv4(),
        isNew: true,
        order: images.value.length + 1, // új képek az utolsó helyre kerülnek
        caption:  '',
        file,
        url: e.target.result,
      })
    }
    reader.readAsDataURL(file)
  })

  console.log('Updated images:', images.value);
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
  if (!props.hasMainImage) return;

  if (img.id) {
    mainImageId.value = img.id;
    mainImageUuid.value = null;
    emit('updateMainImageId', img.id);
  } else if (img.uuid) {
    mainImageUuid.value = img.uuid;
    mainImageId.value = null;
    emit('updateMainImageId', null); // még nincs ID, csak később lesz
  }
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
  const newImages = images.value.filter(i => i.isNew);
  if (!newImages.length) return;

  uploading.value = true;

  // Ha section és nincs még ID, próbáljuk UID alapján kinyerni
  if (!props.modelId && props.modelType === 'AmigurumiSection' && props.modelUid && typeof props.getSectionIdByUid === 'function') {
    const newId = props.getSectionIdByUid(props.modelUid);
    if (newId) {
      const fixedEndpoint = `/api/sections/${newId}/images`;
      console.log('Frissített endpoint section-nek:', fixedEndpoint);
      await uploadImagesTo(newImages, fixedEndpoint, newId);
    } else {
      console.error('Nem található ID a megadott UID alapján:', props.modelUid);
    }
  } else if (!props.modelId && props.modelType === 'AmigurumiPatternAssemblyStep' && props.modelUid && typeof props.getSectionIdByUid === 'function') {
    const newId = props.getAssemblyStepIdByUid(props.modelUid);
    if (newId) {
      const fixedEndpoint = `/api/assemblystep/${newId}/images`;
      console.log('Frissített endpoint assemblystep-nek:', fixedEndpoint);
      await uploadImagesTo(newImages, fixedEndpoint, newId);
    } else {
      console.error('Nem található ID a megadott UID alapján:', props.modelUid);
    }
  } else {
    const endpoint = endpointMap[props.modelType];
    await uploadImagesTo(newImages, endpoint, props.modelId);
  }

  uploading.value = false;
}
async function uploadImagesTo(imagesToUpload, endpoint, modelId) {
  for (let img of imagesToUpload) {
    let uploadFile = img.file;
    try {
      uploadFile = await convertPngFileToJpgBlob(img.file);
    } catch (e) {
      console.warn('PNG konverzió sikertelen, eredeti fájl használva.', e);
    }

    const formData = new FormData();
    formData.append('image', uploadFile, img.file.name.replace(/\.png$/i, '.jpg'));
    formData.append('order', img.order);
    formData.append('caption', img.caption || '');
    formData.append('is_main',
      props.hasMainImage &&
        (
          (img.id && img.id === mainImageId.value) ||
          (img.uuid && img.uuid === mainImageUuid.value)
        ) ? 1 : 0);
    formData.append('model_type', props.modelType);
    formData.append('model_id', modelId);

    try {
      const res = await fetch('/images/upload', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
      });

      const data = await res.json();

      if (res.ok && data.image?.id && data.image?.url) {
        img.id = data.image.id;
        img.url = data.image.url;
        img.isNew = false;
        delete img.file;
        delete img.uuid;

        if (!mainImageId.value) {
          mainImageId.value = data.image.id;
          emit('updateMainImageId', data.image.id);
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
}



async function convertPngFileToJpgBlob(file, quality = 0.9) {
  if (file.type !== 'image/png') return file; // Csak PNG-t alakítunk át

  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#fff'; // fehér háttér az átlátszó helyekhez
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0);
        canvas.toBlob(
          (blob) => {
            if (blob) {
              resolve(blob);
            } else {
              reject(new Error('Blob konverzió sikertelen'));
            }
          },
          'image/jpeg',
          quality
        );
      };
      img.onerror = reject;
      img.src = e.target.result;
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}



function rotateImage(img) {
  const image = new Image();
  image.crossOrigin = 'anonymous'; // biztonsági okból, ha később szerverről jön
  image.src = img.url;

  image.onload = () => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    const angle = 90;
    const radians = (angle * Math.PI) / 180;

    if (angle % 180 !== 0) {
      canvas.width = image.height;
      canvas.height = image.width;
    } else {
      canvas.width = image.width;
      canvas.height = image.height;
    }

    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate(radians);
    ctx.drawImage(image, -image.width / 2, -image.height / 2);

    canvas.toBlob(blob => {
      if (blob) {
        const fileName = img.file?.name || `rotated_${Date.now()}.jpg`;
        const mimeType = img.file?.type || 'image/jpeg';

        const file = new File([blob], fileName, {
          type: mimeType,
          lastModified: Date.now(),
        });

        // Frissítjük a képet, mintha új lenne
        img.file = file;
        img.toBeReplaced = img.isNew ? false : true; // csak ha nem új, akkor jelöljük meg cserére
       

        // Generálunk új base64 URL-t a preview-hoz
        const reader = new FileReader();
        reader.onload = e => {
          img.url = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    }, 'image/jpeg');
   
  };

  image.onerror = () => {
    toast.error('A kép betöltése forgatáshoz nem sikerült.');
  };
}
async function replaceModifiedExistingImages() {
  const imagesToReplace = images.value.filter(i => i.toBeReplaced);
  if (!imagesToReplace.length) return;

  uploading.value = true;

  for (const img of imagesToReplace) {
    const formData = new FormData();
    formData.append('image', img.file);

    try {
      const res = await fetch(`/images/${img.id}/replace`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData,
      });

      const data = await res.json();

      if (res.ok && data.image?.url) {
        img.url = data.image.url;
        img.toBeReplaced = false;
        delete img.file;
      } else {
        toast.error(data.message || 'Image replace failed');
        console.error('Replace error:', data);
      }
    } catch (err) {
      toast.error('Replace error');
      console.error(err);
    }
  }

  uploading.value = false;
}

function openCaptionModal(img) {
  currentCaptionImage.value = img
  captionDraft.value = img.caption || ''
  captionModalVisible.value = true
}

function closeCaptionModal() {
  captionModalVisible.value = false
  currentCaptionImage.value = null
  captionDraft.value = ''
}

function saveCaption() {
  const img = currentCaptionImage.value
  if (!img) return

  img.caption = captionDraft.value

  // Jelöljük, hogy meglévő képhez később menteni kell
  if (!img.isNew) {
    img.captionChanged = true
  }
  

  toast.success('Caption updated')
  closeCaptionModal()
}

async function saveModifiedCaptions() {
  const changed = images.value.filter(img => img.captionChanged && !img.isNew)

  for (const img of changed) {
    try {
      const res = await fetch(`/images/${img.id}/caption`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ caption: img.caption }),
      })

      const data = await res.json()
      if (res.ok) {
        img.captionChanged = false // reset
      } else {
        toast.error(data.message || 'Caption save failed')
      }
    } catch (err) {
      toast.error('Caption save error')
      console.error(err)
    }
  }
}

function openCropModal(img) {
  croppingImage.value = img
  cropModalVisible.value = true
}

function closeCropModal() {
  cropModalVisible.value = false
  croppingImage.value = null
}

function applyCrop() {
  const result = cropperRef.value.getResult();

  if (!result.canvas) {
    toast.error('Failed to crop image')
    return
  }

  result.canvas.toBlob(blob => {
    if (!blob) return

    const img = croppingImage.value
    const file = new File([blob], `cropped_${Date.now()}.jpg`, {
      type: blob.type,
      lastModified: Date.now(),
    })

    img.file = file
    img.url = URL.createObjectURL(blob)

    // Csak meglévő képeknél jelöljük cserére
    if (!img.isNew) {
      img.toBeReplaced = true
    }

    closeCropModal()
    toast.success('Image cropped successfully')
  }, 'image/jpeg')
}

function isMainImage(img) {
  return (
    (img.id && img.id === mainImageId.value) ||
    (img.uuid && img.uuid === mainImageUuid.value)
  );
}
const computedAspectRatio = computed(() => {
  return selectedAspectRatio.value === null ? undefined : selectedAspectRatio.value
})



defineExpose({
  uploadPendingImages, replaceModifiedExistingImages, saveModifiedCaptions
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
              v-if="hasMainImage"
              type="button"
              class="btn btn-sm ms-1"
              :class="isMainImage(img) ? 'btn-primary' : 'btn-dark'"
              @click="setMainImage(img)"
              title="Set as main image"
            >
              <i class="bi bi-layer-forward"></i>
            </button>

            <button
              type="button"
              class="btn btn-sm btn-warning ms-1"
              @click="rotateImage(img)"
              title="Rotate 90°"
            >
              <i class="bi bi-arrow-clockwise"></i>
            </button>

            <button
              type="button"
              class="btn btn-sm btn-secondary ms-1"
              @click="openCaptionModal(img)"
              title="Edit caption"
            >
              <i class="bi bi-chat-text"></i>
            </button>
            <button
              type="button"
              class="btn btn-sm btn-warning ms-1"
              title="Crop image"
              @click="openCropModal(img)"
            >
             <i class="bi bi-crop"></i>
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
  <input v-if="hasMainImage" type="hidden" name="main_image_id" :value="mainImageId" />

  <teleport to="body">
  <div v-if="captionModalVisible" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Caption</h5>
          <button type="button" class="btn-close" @click="closeCaptionModal"></button>
        </div>
        <div class="modal-body">
          <input v-model="captionDraft" type="text" class="form-control" placeholder="Add caption..." />
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="closeCaptionModal">Cancel</button>
          <button class="btn btn-primary" @click="saveCaption">Save</button>
        </div>
      </div>
    </div>
  </div>
  </teleport>
  <teleport to="body">
    <div v-if="cropModalVisible" class="modal-backdrop">
      <div class="modal-window">
        <Cropper
          :src="croppingImage?.url"
          :stencil-props="{ aspectRatio: selectedAspectRatio }"
          :auto-zoom="true"
          class="cropper"
          ref="cropperRef"
        />
       <div class="mt-2 mb-2">
        <label class="me-2">Crop ratio:</label>

        <label class="me-2">
          <input type="radio" v-model="selectedAspectRatio" :value="null" />
          Free
        </label>

        <label class="me-2">
          <input type="radio" v-model="selectedAspectRatio" :value="1" />
          1:1
        </label>

        <label class="me-2">
          <input type="radio" v-model="selectedAspectRatio" :value="16 / 9" />
          16:9
        </label>

        <label class="me-2">
          <input type="radio" v-model="selectedAspectRatio" :value="4 / 3" />
          4:3
        </label>
      </div>

        <div class="mt-2 d-flex justify-content-end">
          <button class="btn btn-secondary me-2" @click="closeCropModal">Cancel</button>
          <button class="btn btn-primary" @click="applyCrop">Save</button>
        </div>
      </div>
    </div>
  </teleport>


</template>
