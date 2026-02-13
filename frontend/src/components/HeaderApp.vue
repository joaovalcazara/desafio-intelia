<script setup>
  import '../styles/header-app.css'
  defineProps({
    etapa: {
      type: Number,
      required: true
    }
  });
</script>

<template>
  <header class="intelia-navbar">
    <div class="header-container">
      <div class="top-row">
        <img 
          src="https://i.postimg.cc/9XkMTy8g/97-33-logo-Prancheta-1.png" 
          alt="Intelia" 
          class="logo-img"
        >
        <div class="step-badge">
          Passo {{ etapa }} de 3
        </div>
      </div>

      <div class="event-info">
        <h1 class="event-name">
          Utilizando as soluções da Intelia para vender mais através do telefone e whatsapp
        </h1>
      </div>

      <div class="progress-track">
        <div class="progress-fill" :style="{ width: (etapa / 3) * 100 + '%' }"></div>
      </div>
    </div>
  </header>
</template> 

 