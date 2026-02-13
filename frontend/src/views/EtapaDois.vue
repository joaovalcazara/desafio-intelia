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
          <v-text-field v-model="form.cep" v-maska="'#####-###'" label="CEP" required placeholder="00000-000" />
        </div>
        <div class="md-field">
          <v-text-field v-model="form.rua" label="Rua" required placeholder="Ex: Av. Paulista" />
        </div>
      </div>

      <div class="md-grid">
        <div class="md-field">
          <v-text-field v-model="form.numero" label="Número" required />
        </div>
        <div class="md-field">
          <v-text-field v-model="form.cidade" label="Cidade" required />
        </div>
      </div>

      <div class="md-field">
        <v-text-field v-model="form.estado" label="Estado (UF)" maxlength="2" required placeholder="PR" />
      </div>

      <div class="md-actions">
        <v-btn type="button" @click="$emit('voltar')" class="md-button text">Voltar</v-btn>
        <v-btn type="submit" class="md-button primary" :disabled="loading">Próximo</v-btn>
      </div>
    </form>

    <MensagemAlert 
      v-model="showError" 
      :message="errorMessage" 
      type="error" 
    />
  </div>
</template>