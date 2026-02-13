<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import EtapaUm from './views/EtapaUm.vue';
import EtapaDois from './views/EtapaDois.vue';
import EtapaTres from './views/EtapaTres.vue';
import HeaderApp from './components/HeaderApp.vue';
import FooterApp from './components/FooterApp.vue';
import './styles/app.css';
const etapa = ref(1);
const uuid = ref(localStorage.getItem('cadastro_uuid'));
const dadosIniciais = ref(null);
const views = [EtapaUm, EtapaDois, EtapaTres];

const carregarDados = async (setEtapa = true) => {
  if (uuid.value) {
    try {
      const { data } = await axios.get(`/cadastro/${uuid.value}`);
      dadosIniciais.value = { ...data.data };
      if (setEtapa && data?.data?.etapaAtual) {
        etapa.value = data.data.etapaAtual;
      }
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
  etapa.value = Math.max(1, etapa.value - 1);
  await carregarDados(false);
};
</script>


<template>
  <v-app>
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
    <FooterApp />
  </v-app>
</template>
 

 