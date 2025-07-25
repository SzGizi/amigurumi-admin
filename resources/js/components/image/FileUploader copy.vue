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

const emit = defineEmits(['updateDeletedImages','updateMainImageId']);

const props = defineProps({
  modelType: String,
  modelId: Number,
  name: String,
  patternMainImageId: Number,
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
const mainImageId = props.patternMainImageId // fő kép ID


onMounted(async () => {
  
  if (!endpoint) {
    console.error('Unknown modelType:', props.modelType)
  } else {
    const res = await fetch(endpoint)
    const images = await res.json()
     console.log('Fetched images:', images)

    pondFiles.value = images
      .filter(image => image && image.id && image.url)
      .map(image => ({
        source: image.url,
        options: {
          type: 'local',
          metadata: {
            id: image.id,
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
      load(res.image.url)  // itt fontos: load hívás a feltöltött kép azonosítójával
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

function handleProcessFile(error, file) {
  if (error) {
    console.error('File upload error:', error);
    return;
  }
  
  // Feltételezem, hogy a szerver response-ban visszakapjuk az új kép URL-jét és id-jét
  const imageId = file.serverId; // ez amit a server.process 'load(res.image.id)' -ben adsz meg
  const imageUrl = file.getMetadata('url'); // vagy máshonnan kell megszerezni

  // Ha nem áll rendelkezésre a url, akkor lehet, hogy az 'file.file' objektumot használjuk blob-ként, de ez nem url
  // ezért inkább dinamikusan adjuk hozzá az új képet az URL-lel amit a szerver küld vissza

  // Erre jobb megoldás, hogy a process függvényedben a feltöltés után kapott URL-t is visszaadod metadata-ként
  // Ehhez a process metódusodban a load hívásakor add át a teljes adatot (id+url), pl:
  // load({ id: res.image.id, url: res.image.url })

  // Itt a file.serverId helyett inkább ezt használd

  const newFileMetadata = file.getMetadata(); // ha átadtál url-t a metadata-ban

  if (!newFileMetadata.url) {
    console.warn('No image URL available for new file');
    return;
  }

  // Új kép hozzáadása pondFiles-hoz
  pondFiles.value.push({
    source: newFileMetadata.url,
    options: {
      type: 'local',
      metadata: {
        id: imageId,
        url: newFileMetadata.url,
      }
    }
  })
}



</script>

<template>
 <FilePond
    name="images"
    :files="pondFiles"
    allow-multiple
    :server="server"
    :allowRevert="false"
    @processfile="handleProcessFile"
  />  
  <div class="preview-main-buttons">
    <div v-for="file in pondFiles" :key="file.options.metadata.id" class="mt-2 d-flex align-items-center">
      <img :src="file.options.metadata.url" class="preview-image">
      <button
      type="button"
        class="btn btn-sm"
        :class="{'btn-primary': mainImageId === file.options.metadata.id, 'btn-outline-primary': mainImageId !== file.options.metadata.id}"
        @click="
        mainImageId = file.options.metadata.id; 
        emit('updateMainImageId', mainImageId);"
      >
        Set as Main
      </button>
    </div> 
  </div>


  

    <input
    type="hidden"
    name="deleted_image_ids"
    :value="pendingDeleteIds.join(',')"
    />
    <input
    type="hidden"
    name="main_image_id"
    :value="mainImageId"
  />
 


</template>
