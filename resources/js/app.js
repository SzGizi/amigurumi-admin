import './bootstrap';
import 'bootstrap';
import { createApp } from 'vue';
import axios from 'axios';
import AmigurumiPatternEdit from './components/AmigurumiPatternEdit.vue';

import Alpine from 'alpinejs';

const app = createApp({});
app.component('amigurumi-pattern-edit', AmigurumiPatternEdit);
app.mount('#app');


window.Alpine = Alpine;
Alpine.start();

