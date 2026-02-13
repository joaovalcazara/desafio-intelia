import { createApp } from 'vue'
import './style.css'
import 'vuetify/styles'
import App from './App.vue'
import axios from 'axios';
import vuetify from './plugins/vuetify'

// Em dev
axios.defaults.baseURL = 'http://localhost/api';

// Em prod 
// axios.defaults.baseURL = window.location.origin + '/api';

const app = createApp(App)
app.use(vuetify)
app.mount('#app')
