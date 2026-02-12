<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import EtapaUm from './views/EtapaUm.vue';
import EtapaDois from './views/EtapaDois.vue';
import EtapaTres from './views/EtapaTres.vue';
import HeaderApp from './components/HeaderApp.vue';
import './styles/app.css';
const etapa = ref(1);
const uuid = ref(localStorage.getItem('cadastro_uuid'));
const dadosIniciais = ref(null);
const views = [EtapaUm, EtapaDois, EtapaTres];

const carregarDados = async () => {
  if (uuid.value) {
    try {
      const { data } = await axios.get(`/cadastro/${uuid.value}`);
      dadosIniciais.value = { ...data.data }; 
      etapa.value = data.data.etapaAtual; 
    } catch (e) {
      localStorage.removeItem('cadastro_uuid');
    }
  }
};

onMounted(() => {
  carregarDados();
});

const proximo = (novoUuid) => {
  if (novoUuid) {
    uuid.value = novoUuid;
    localStorage.setItem('cadastro_uuid', novoUuid);
  }
  etapa.value++;
};

const voltar = async () => {
  etapa.value--;
  await carregarDados();
};
</script>


<template>
  <HeaderApp :etapa="etapa" /> 
  <main class="main-container">
    <div class="form-card">
      <transition name="fade" mode="out-in">
        <component 
          :is="views[etapa - 1]" 
          :uuid="uuid"
          :dados-iniciais="dadosIniciais"
          @avancar="proximo" 
          @voltar="voltar"
        />
      </transition>
    </div>
  </main>
</template>
 

 