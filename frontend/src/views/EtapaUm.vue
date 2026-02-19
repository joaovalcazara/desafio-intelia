<script setup>
import { reactive, ref, watch, computed } from 'vue';
import axios from 'axios';
import '../styles/form-etapas.css';
import { useI18n } from 'vue-i18n'
    

const emit = defineEmits(['avancar']);
const loading = ref(false);
const dateMenu = ref(false);
const props = defineProps(['uuid', 'dadosIniciais']);
const diaAtual = new Date().toISOString().split('T')[0]
const { t, locale } = useI18n()

const dateLocale = computed(() => {
  if (!locale || !locale.value) return 'pt-BR'
  switch (locale.value) {
    case 'pt': return 'pt-BR'
    case 'es': return 'es-ES'
    case 'en': return 'en-US'
    default: return locale.value
  }
})

const formattedDate = computed(() => {
  return form.dataNascimento ? new Date(form.dataNascimento).toLocaleDateString(dateLocale.value) : ''
})
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
      <h2 class="md-title" style="margin-bottom: 20px;">{{ t('identificacao') }}</h2>
    </div> 
    <form @submit.prevent="avancar" class="md-form">
      <div class="md-field">
        <v-text-field
          v-model="form.nomeCompleto"
          :label="t('nome_completo')"
          required
          :placeholder="t('digite_seu_nome')"
        />
      </div> 
      <div class="md-field">
        <v-menu v-model="dateMenu" location="bottom start" :close-on-content-click="false">
          <template #activator="{ props }">
            <v-text-field
              v-bind="props"
              :model-value="formattedDate"
              :label="t('data_nascimento')"
              :placeholder="t('digite_data')"
              readonly
              required
            />
          </template>
          <v-date-picker v-model="form.dataNascimento" :locale="dateLocale" :max="diaAtual" @update:model-value="() => (dateMenu = false)" />
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
          {{ loading ?  t('enviando') : t('proximo') }}
        </v-btn>
      </div>
    </form>
  </div>
</template>

 