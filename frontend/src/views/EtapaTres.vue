<script setup>
    import { reactive, ref } from 'vue';
    import { vMaska } from "maska/vue";
    import axios from 'axios';
    import MensagemAlert from '../components/MensagemAlert.vue';

    
    const props = defineProps(['uuid', 'dadosIniciais']);
    const emit = defineEmits(['voltar', 'sucesso']);

    const loading = ref(false); 

    const showAlert = ref(false);
    const alertMessage = ref('');
    const alertType = ref('error');

    const form = reactive(props.dadosIniciais || {
        telefoneFixo: '', 
        telefoneCelular: '',  
        etapaAtual: 3
    });

    const finalizar = async () => {
        loading.value = true;
        try {
            const payload = {
                uuid: props.uuid,
                ...form,
                etapaAtual: 3
            };

            const { data } = await axios.post('/cadastro', payload);
            
            if (data.status === 'sucesso' || data.uuid) { 
                alertType.value = 'success';
                alertMessage.value = "Cadastro finalizado com sucesso! Reiniciando...";
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
        <label>Telefone Fixo</label>
        <input 
          v-model="form.telefoneFixo" 
          v-maska="'(##) ####-####'" 
          required 
          placeholder="(00) 0000-0000"
        >
      </div>

      <div class="md-field">
        <label>Telefone Celular</label>
        <input 
          v-model="form.telefoneCelular" 
          v-maska="'(##) #####-####'" 
          required 
          placeholder="(00) 00000-0000"
        >
      </div>

      <div class="md-actions">
        <button type="button" @click="$emit('voltar')" class="md-button text" :disabled="loading">
          Voltar
        </button>
        <button type="submit" class="md-button success" :disabled="loading">
          {{ loading ? 'Enviando...' : 'Concluir Cadastro' }}
        </button>
      </div>
    </form> 
    <MensagemAlert 
      v-model="showAlert" 
      :message="alertMessage" 
      :type="alertType" 
    />  
  </div>
</template>

