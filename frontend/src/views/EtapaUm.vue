<script setup>
import { reactive, ref, watch} from 'vue';
import axios from 'axios';
import '../styles/form-etapas.css';

const emit = defineEmits(['avancar']);
const loading = ref(false);
const props = defineProps(['uuid', 'dadosIniciais']);

const form = reactive({
  nomeCompleto: '',
  dataNascimento: '',
  email: '',
  etapaAtual: 1
});

 
watch(() => props.dadosIniciais, (novosDados) => {
    if (novosDados) {
      Object.assign(form, novosDados);
    }
  }, { immediate: true });

const avancar = async () => {
  loading.value = true;
  try {
    const payload = {
      uuid: props.uuid, 
      ...form,
      etapaAtual: 1
    };
    
    const { data } = await axios.post('/cadastro', payload);
    emit('avancar', data.data.uuid); 
  } catch (e) {
    if (e.response?.status === 404) {
        localStorage.removeItem('cadastro_uuid');
        uuid.value = null;
    }
  } finally {
    loading.value = false;
  }
};
</script>

 

<template>
  <div class="step-view">
    <div class="form-header">
      <h2 class="md-title" style="margin-bottom: 20px;">Identificação</h2>
    </div>
    
    <form @submit.prevent="avancar" class="md-form">
      <div class="md-field">
        <label>Nome Completo</label>
        <input v-model="form.nomeCompleto" type="text" required placeholder="Digite seu nome">
      </div>
      
      <div class="md-field">
        <label>Data de Nascimento</label>
        <input v-model="form.dataNascimento" type="date" required>
      </div>

      <div class="md-field">
        <label>E-mail</label>
        <input v-model="form.email" type="email" required placeholder="seu@email.com">
      </div>

      <div class="actions actions-etapa-um">
        <v-btn type="submit" class="md-button primary" :disabled="loading">
          {{ loading ? 'Enviando...' : 'Próximo' }}
        </v-btn>
      </div>
    </form>
  </div>
</template>

 