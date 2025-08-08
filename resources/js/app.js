import './bootstrap';
//import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap/dist/css/bootstrap.min.css'

import Sortable from 'sortablejs';
import '../sass/app.scss';

import { createApp } from 'vue';
import axios from 'axios';
import AmigurumiPatternEdit from './components/AmigurumiPatternEdit.vue';
import SocialLinkEdit from './components/SocialLinkEdit.vue';



import Alpine from 'alpinejs';
import Toast from "vue3-toastify";
import "vue3-toastify/dist/index.css";
// main.js vagy setup fájlban
import 'vue-advanced-cropper/dist/style.css';





const app = createApp({});
app.component('amigurumi-pattern-edit', AmigurumiPatternEdit);
app.component('solcial-link-edit', SocialLinkEdit);

app.mount('#app');
app.use(Toast);

window.Alpine = Alpine;
Alpine.start();

