import { createApp } from 'vue'
import './style.css'
import App from './App.vue'
import axios from 'axios'; 

// Em dev
axios.defaults.baseURL = 'http://localhost/api';

// Em prod 
// axios.defaults.baseURL = window.location.origin + '/api';

createApp(App).mount('#app')
