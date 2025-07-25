import './bootstrap';
//import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import Sortable from 'sortablejs';
import '../sass/app.scss';

import { createApp } from 'vue';
import axios from 'axios';
import AmigurumiPatternEdit from './components/AmigurumiPatternEdit.vue';

import Alpine from 'alpinejs';
import Toast from "vue3-toastify";
import "vue3-toastify/dist/index.css";




const app = createApp({});
app.component('amigurumi-pattern-edit', AmigurumiPatternEdit);
app.mount('#app');
app.use(Toast);

window.Alpine = Alpine;
Alpine.start();

