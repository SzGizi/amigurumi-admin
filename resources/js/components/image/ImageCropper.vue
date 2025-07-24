<script setup>
import { ref } from 'vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const props = defineProps({ image: String })
const emit = defineEmits(['cropped'])

const cropper = ref(null)

function emitCropped() {
  const canvas = cropper.value.getResult().canvas
  emit('cropped', canvas.toDataURL())
}
</script>

<template>
  <div class="cropper-wrapper">
    <cropper
      ref="cropper"
      :src="image"
      :stencil-props="{ aspectRatio: 1 }"
      @change="onChange"
    />
    <button @click="emitCropped">Crop</button>
  </div>
</template>
