<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import '../styles/seletor-linguagem.css'

const { locale } = useI18n()
const options = [
  { value: 'pt', label: 'Português' },
  { value: 'en', label: 'English' },
  { value: 'es', label: 'Español' }
]

const selected = ref(locale.value)

function change() {
  locale.value = selected.value
  localStorage.setItem('locale', selected.value)
  window.location.reload()
}
</script>

<template>
  <div class="language-selector">
    <select v-model="selected" @change="change">
      <option v-for="opt in options" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
    </select>
  </div>
</template>

 