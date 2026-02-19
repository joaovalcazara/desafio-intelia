import { createApp } from 'vue'
import { createI18n } from 'vue-i18n'
import './style.css'
import 'vuetify/styles'
import App from './App.vue'
import axios from 'axios';
import vuetify from './plugins/vuetify'
import messages from './locales'

// Em dev
axios.defaults.baseURL = 'http://localhost/api';

// Em prod 
// axios.defaults.baseURL = window.location.origin + '/api';

const savedLocale = localStorage.getItem('locale') || (navigator.language && navigator.language.split('-')[0]) || 'pt'

const i18n = createI18n({
	legacy: false,
	locale: Object.prototype.hasOwnProperty.call(messages, savedLocale) ? savedLocale : 'pt',
	fallbackLocale: 'pt',
	messages
})

const app = createApp(App)
app.use(vuetify)
app.use(i18n)
app.mount('#app')
