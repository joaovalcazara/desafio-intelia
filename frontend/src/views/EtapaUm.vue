<script setup>
import { reactive, ref, watch } from 'vue';
import axios from 'axios';
import '../styles/form-etapas.css';

const emit = defineEmits(['avancar']);
const loading = ref(false);
const dateMenu = ref(false);
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
    const Uuid = typeof props.uuid === 'string' && props.uuid.trim() !== '';

    let response;
    if (Uuid) {
      response = await axios.put(`/cadastro/${props.uuid}`, payload);
    } else {
      response = await axios.post('/cadastro', payload);
    }

    emit('avancar', response.data.data.uuid);
  } catch (e) {
    localStorage.removeItem('cadastro_uuid');
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
        <v-text-field
          v-model="form.nomeCompleto"
          label="Nome Completo"
          required
          placeholder="Digite seu nome"
        />
      </div> 
      <div class="md-field">
        <v-menu v-model="dateMenu" location="bottom start" :close-on-content-click="false">
          <template #activator="{ props }">
            <v-text-field
              v-bind="props"
              :model-value="form.dataNascimento ? new Date(form.dataNascimento).toLocaleDateString('pt-BR') : ''"
              label="Data de Nascimento"
              readonly
              required
            />
          </template>
          <v-date-picker v-model="form.dataNascimento" @update:model-value="() => (dateMenu = false)" />
        </v-menu>
      </div> 
      <div class="md-field">
        <v-text-field
          v-model="form.email"
          label="E-mail"
          type="email"
          required
          placeholder="seu@email.com"
        />
      </div> 
      <div class="actions actions-etapa-um">
        <v-btn type="submit" class="md-button primary" :disabled="loading">
          {{ loading ? 'Enviando...' : 'Próximo' }}
        </v-btn>
      </div>
    </form>
  </div>
</template>

 