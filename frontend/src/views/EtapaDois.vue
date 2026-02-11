<script setup>
import { reactive, ref } from 'vue';
import { vMaska } from "maska/vue";
import axios from 'axios';

const props = defineProps(['uuid', 'dadosIniciais']);
const emit = defineEmits(['avancar', 'voltar']);
const loading = ref(false);

const form = reactive(props.dadosIniciais || {
    cep: '', 
    rua: '', 
    numero: '', 
    cidade: '', 
    estado: '',
    etapaAtual: 2
});

const avancar = async () => {
  loading.value = true;
  try {
    const payload = {
      uuid: props.uuid,  
      ...form,
      etapaAtual: 2
    };
    await axios.post('/cadastro', payload);
    emit('avancar');
  } catch (e) {
    alert(e.response?.data?.message || "Erro no Passo 2");
  } finally {
    loading.value = false;
  }
};
</script>


<template>
  <div class="step-view">
    <h2 class="md-title">Endereço</h2>
    <p class="md-subtitle">Onde podemos te encontrar?</p>
    
    <form @submit.prevent="avancar">
      <div class="md-grid">
        <div class="md-field">
          <label>CEP</label>
          <input v-model="form.cep" v-maska="'#####-###'" required placeholder="00000-000">
        </div>
        <div class="md-field">
          <label>Rua</label>
          <input v-model="form.rua" type="text" required placeholder="Ex: Av. Paulista">
        </div>
      </div>

      <div class="md-grid">
        <div class="md-field">
          <label>Número</label>
          <input v-model="form.numero" type="text" required>
        </div>
        <div class="md-field">
          <label>Cidade</label>
          <input v-model="form.cidade" type="text" required>
        </div>
      </div>

      <div class="md-field">
        <label>Estado (UF)</label>
        <input v-model="form.estado" type="text" maxlength="2" required placeholder="SP">
      </div>

      <div class="md-actions">
        <button type="button" @click="$emit('voltar')" class="md-button text">Voltar</button>
        <button type="submit" class="md-button primary" :disabled="loading">Próximo</button>
      </div>
    </form>
  </div>
</template>