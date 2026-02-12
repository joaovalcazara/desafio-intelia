<script setup>
import { reactive, ref, watch } from 'vue';
import { vMaska } from "maska/vue";
import axios from 'axios';
import MensagemAlert from '../components/MensagemAlert.vue';

const props = defineProps(['uuid', 'dadosIniciais']);
const emit = defineEmits(['avancar', 'voltar']);
const loading = ref(false); 
const showError = ref(false);
const errorMessage = ref('');


const form = reactive({
    cep: '', 
    rua: '', 
    numero: '', 
    cidade: '', 
    estado: '',
    etapaAtual: 2
});

watch(() => props.dadosIniciais, (novosDados) => {
  if (novosDados) {
    Object.assign(form, novosDados);
  }
}, { immediate: true, deep: true });

const avancar = async () => {
  const cepRegex = /^\d{5}-\d{3}$/;
  if (!cepRegex.test(form.cep)) {
    errorMessage.value = "CEP inválido! Use o formato: 00000-000";
    showError.value = true;
    return;
  }
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
    errorMessage.value = e.response?.data?.message || "Erro ao avançar para a próxima etapa.";
    showError.value = true; 
  } finally {
    loading.value = false;
  }
};
</script>


<template>
  <div class="step-view">
    <h2 class="md-title" style="margin-bottom: 20px;">Endereço</h2> 
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
        <input v-model="form.estado" type="text" maxlength="2" required placeholder="PR">
      </div>

      <div class="md-actions">
        <button type="button" @click="$emit('voltar')" class="md-button text">Voltar</button>
        <button type="submit" class="md-button primary" :disabled="loading">Próximo</button>
      </div>
    </form>

    <MensagemAlert 
      v-model="showError" 
      :message="errorMessage" 
      type="error" 
    />
  </div>
</template>