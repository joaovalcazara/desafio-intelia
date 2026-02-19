<script setup>
import { reactive, ref, watch } from 'vue';
import { vMaska } from "maska/vue";
import axios from 'axios';
import MensagemAlert from '../components/MensagemAlert.vue';
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

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

const buscarCep = async () => {
  const digits = String(form.cep || '').replace(/\D/g, '');
  if (digits.length !== 8) return;  
  try {
    loading.value = true;
    const res = await axios.get(`https://viacep.com.br/ws/${digits}/json/`);
    const data = res.data;
    if (data && !data.erro) {
      form.rua = data.logradouro || form.rua;
      form.cidade = data.localidade || form.cidade;
      form.estado = (data.uf || form.estado).toUpperCase().slice(0, 2);
    } else {
      errorMessage.value = t('cep_not_found');
      showError.value = true;
    }
  } catch (e) {
    errorMessage.value = t('cep_error');
    showError.value = true;
  } finally {
    loading.value = false;
  }
};

watch(() => props.dadosIniciais, (novosDados) => {
  if (novosDados) {
    Object.assign(form, novosDados);
  }
}, { immediate: true, deep: true });

watch(() => form.cep, (novo) => {
  const cepCompleto = /^\d{5}-\d{3}$/.test(novo);
  if (cepCompleto) buscarCep();
});

const avancar = async () => {
  const cepRegex = /^\d{5}-\d{3}$/;
  if (!cepRegex.test(form.cep)) {
    errorMessage.value = t('cep_invalid');
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

    if (!props.uuid) {
      errorMessage.value = t('cadastro_nao_iniciado');
      showError.value = true;
      return;
    }

    await axios.put(`/cadastro/${props.uuid}`, payload);
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
    <h2 class="md-title" style="margin-bottom: 20px;">{{ t('endereco') }}</h2> 
    <form @submit.prevent="avancar" >
      <div class="md-grid">
        <div class="md-field">
          <v-text-field v-model="form.cep" v-maska="'#####-###'" :label="t('cep_label')" required :placeholder="t('cep_placeholder')" />
        </div>
        <div class="md-field">
          <v-text-field v-model="form.rua" :label="t('rua_label')" required :placeholder="t('rua_placeholder')" />
        </div>
      </div>

      <div class="md-grid">
        <div class="md-field">
          <v-text-field v-model="form.numero" type="number" :label="t('numero_label')" required />
        </div>
        <div class="md-field">
          <v-text-field v-model="form.cidade" :label="t('cidade_label')" required />
        </div>
      </div>

      <div class="md-field">
        <v-text-field v-model="form.estado" type="text" maxlength="2" :label="t('estado_label')" required placeholder="PR" />
      </div>

      <div class="md-actions">
        <v-btn type="button" @click="$emit('voltar')" class="md-button text">{{ t('voltar') }}</v-btn>
        <v-btn type="submit" class="md-button primary" :disabled="loading">{{ t('proximo') }}</v-btn>
      </div>
    </form>

    <MensagemAlert 
      v-model="showError" 
      :message="errorMessage" 
      type="error" 
    />
  </div>
</template>