<script setup>
    import { reactive, ref, watch } from 'vue';
    import { vMaska } from "maska/vue";
    import axios from 'axios';
    import MensagemAlert from '../components/MensagemAlert.vue';
    import { useI18n } from 'vue-i18n'

    
    const props = defineProps(['uuid', 'dadosIniciais']);
    const emit = defineEmits(['voltar', 'sucesso']); 
    const loading = ref(false);  
    const showAlert = ref(false);
    const alertMessage = ref('');
    const alertType = ref('error'); 
    const { t } = useI18n()
    const form = reactive({
        telefoneFixo: '', 
        telefoneCelular: '',  
        etapaAtual: 3
    });

    watch(() => props.dadosIniciais, (novosDados) => {
      if (novosDados) {
        Object.assign(form, novosDados);
      }
    }, { immediate: true, deep: true });


    const finalizar = async () => {
        const celularRegex = /^\(\d{2}\) \d{5}-\d{4}$/;
        if (!celularRegex.test(form.telefoneCelular)) {
          alertType.value = 'error';
          alertMessage.value = t('telefone_celular_invalid');
          showAlert.value = true;
            return;
        }

        const fixoRegex = /^\(\d{2}\) \d{4}-\d{4}$/;
        if (form.telefoneFixo && !fixoRegex.test(form.telefoneFixo)) {
          alertType.value = 'error';
          alertMessage.value = t('telefone_fixo_invalid');
          showAlert.value = true;
            return;
        }

        loading.value = true;
        try {
          const payload = {
            uuid: props.uuid,
            ...form,
            etapaAtual: 3
          };

          if (!props.uuid) {
            alertType.value = 'error';
            alertMessage.value = t('cadastro_nao_iniciado');
            showAlert.value = true;
            loading.value = false;
            return;
          }

          const { data } = await axios.put(`/cadastro/${props.uuid}`, payload);

          if (data.status === 'sucesso' || data.uuid) { 
                alertType.value = 'success';
                alertMessage.value = t('cadastro_finalizado');
                showAlert.value = true; 
                setTimeout(() => {
                    localStorage.removeItem('cadastro_uuid');
                    emit('sucesso');
                    window.location.reload();
                }, 2500); 

            } else {
                throw new Error("Resposta inesperada do servidor");
            }
        } catch (e) {
            alertType.value = 'error';
            alertMessage.value = e.response?.data?.message || "Erro ao finalizar cadastro.";
            showAlert.value = true;
        } finally {
            loading.value = false;
        }
    };
</script>

<template>
  <div class="step-view">
    <h2 class="md-title" style="margin-bottom: 20px;">{{ t('contato') }}</h2>    
    <form @submit.prevent="finalizar">

      <div class="md-field">
        <v-text-field
          v-model="form.telefoneCelular"
          v-maska="'(##) #####-####'"
           :label="t('telefone_celular_label')"
          required
             :placeholder="t('telefone_celular_placeholder')"
        />
      </div>  
      <div class="md-field">
        <v-text-field
          v-model="form.telefoneFixo"
          v-maska="'(##) ####-####'"
          :label="t('telefone_fixo_label')"
          :placeholder="t('telefone_fixo_placeholder')"
        />
      </div>  
      <div class="md-actions">
        <v-btn type="button" @click="$emit('voltar')" class="md-button text" :disabled="loading">
          {{ t('voltar') }}
        </v-btn>
        <v-btn type="submit" class="md-button success" :disabled="loading">
          {{ loading ? t('enviando') : t('concluir_cadastro') }}
        </v-btn>
      </div>
    </form> 
    <MensagemAlert 
      v-model="showAlert" 
      :message="alertMessage" 
      :type="alertType" 
    />  
  </div>
</template>

