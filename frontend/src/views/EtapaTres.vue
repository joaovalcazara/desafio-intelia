<script setup>
    import { reactive, ref } from 'vue';
    import { vMaska } from "maska/vue";
    import axios from 'axios';

    const props = defineProps(['uuid', 'dadosIniciais']);
    const emit = defineEmits(['voltar', 'sucesso']);

    const loading = ref(false); 

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
        
        if (data.status === 'sucesso') {
            emit('sucesso');
            alert("Cadastro finalizado com sucesso!");
        } else {
            alert("Erro ao finalizar cadastro. Tente novamente.");
        }
    } catch (e) {
        alert(e.response?.data?.message || "Erro ao finalizar cadastro.");
    } finally {
        loading.value = false;
    }
    };
</script>

<template>
  <div class="step-view">
    <h2 class="md-title">Contato Final</h2>
    <p class="md-subtitle">Quase lá! Precisamos apenas dos seus contatos.</p>
    
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
  </div>
</template>

