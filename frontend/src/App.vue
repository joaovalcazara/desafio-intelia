<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import EtapaUm from './views/EtapaUm.vue';
import EtapaDois from './views/EtapaDois.vue';
import EtapaTres from './views/EtapaTres.vue';

const etapa = ref(1);
const uuid = ref(localStorage.getItem('cadastro_uuid'));
const dadosIniciais = ref(null);
const views = [EtapaUm, EtapaDois, EtapaTres];

onMounted(async () => {
  if (uuid.value) {
    try {
      const { data } = await axios.get(`/cadastro/${uuid.value}`);
      // Seu controller retorna 'data' dentro de 'data'
      dadosIniciais.value = data.data; 
      etapa.value = data.data.etapaAtual; // Retoma na etapa exata onde parou
    } catch (e) {
      console.error("Cadastro não encontrado, iniciando do zero.");
      localStorage.removeItem('cadastro_uuid');
    }
  }
});
const proximo = (novoUuid) => {
  if (novoUuid) {
    uuid.value = novoUuid;
    localStorage.setItem('cadastro_uuid', novoUuid);
  }
  etapa.value++;
};
</script>


<template>
  <div class="app-wrapper">
    <div class="card md-elevation-2">
      
 

      <transition name="fade" mode="out-in">
        <component 
          :is="views[etapa - 1]" 
          :uuid="uuid"
          :dados-iniciais="dadosIniciais"
          @avancar="proximo"
          @voltar="etapa--"
          @sucesso="finalizarTudo"
        />
      </transition>

    </div>
  </div>
</template>