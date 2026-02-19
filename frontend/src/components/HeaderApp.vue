<script setup>
  import '../styles/header-app.css'
  import SeletorLinguagem from './SeletorLinguagem.vue'
  import { useI18n } from 'vue-i18n'

  defineProps({
    etapa: {
      type: Number,
      required: true
    }
  });

  const { t } = useI18n()
</script>

<template>
  <header class="intelia-navbar">
    <div class="header-container">
      <div class="top-row">
        <img 
          src="https://i.postimg.cc/9XkMTy8g/97-33-logo-Prancheta-1.png" 
          :alt="t('logo_alt')" 
          class="logo-img"
        >
        <div class="right-controls">
      
          <div class="step-badge">
            {{ t('passo_de', { etapa: etapa, total: 3 }) }}
          </div>
        </div>
      </div>

      <div class="event-info">
        <h1 class="event-name">
          {{ t('event_name') }}
        </h1>
      </div>

      <div>
        <SeletorLinguagem />
      </div>

      <div class="progress-track">
        <div class="progress-fill" :style="{ width: (etapa / 3) * 100 + '%' }"></div>
      </div>
    </div>
  </header>
</template> 

 