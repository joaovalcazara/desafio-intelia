<script setup>
    import { reactive, ref, watch } from 'vue';
    import { vMaska } from "maska/vue";
    import axios from 'axios';
    import MensagemAlert from '../components/MensagemAlert.vue';

    
    const props = defineProps(['uuid', 'dadosIniciais']);
    const emit = defineEmits(['voltar', 'sucesso']); 
    const loading = ref(false);  
    const showAlert = ref(false);
    const alertMessage = ref('');
    const alertType = ref('error'); 
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
            alertMessage.value = "Telefone celular inválido! Use o formato: (00) 00000-0000";
            showAlert.value = true;
            return;
        }

        const fixoRegex = /^\(\d{2}\) \d{4}-\d{4}$/;
        if (form.telefoneFixo && !fixoRegex.test(form.telefoneFixo)) {
            alertType.value = 'error';
            alertMessage.value = "Telefone fixo inválido! Use o formato: (00) 0000-0000";
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
            alertMessage.value = 'Cadastro não iniciado. Volte para a etapa 1.';
            showAlert.value = true;
            loading.value = false;
            return;
          }

          const { data } = await axios.put(`/cadastro/${props.uuid}`, payload);

          if (data.status === 'sucesso' || data.uuid) { 
                alertType.value = 'success';
                alertMessage.value = "Cadastro finalizado com sucesso! Obrigado pela preferência.";
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
    <h2 class="md-title" style="margin-bottom: 20px;">Contato</h2>    
    <form @submit.prevent="finalizar">

      <div class="md-field">
        <v-text-field
          v-model="form.telefoneCelular"
          v-maska="'(##) #####-####'"
          label="Telefone Celular"
          required
          placeholder="(00) 00000-0000"
        />
      </div>  
      <div class="md-field">
        <v-text-field
          v-model="form.telefoneFixo"
          v-maska="'(##) ####-####'"
          label="Telefone Fixo"
          placeholder="(00) 0000-0000"
        />
      </div>  
      <div class="md-actions">
        <v-btn type="button" @click="$emit('voltar')" class="md-button text" :disabled="loading">
          Voltar
        </v-btn>
        <v-btn type="submit" class="md-button success" :disabled="loading">
          {{ loading ? 'Enviando...' : 'Concluir Cadastro' }}
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

