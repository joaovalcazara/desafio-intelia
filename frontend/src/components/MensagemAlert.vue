 <script setup>
    import { computed, watch } from 'vue';
    import '../styles/mensagemAlert.css';

    const props = defineProps({
    modelValue: Boolean,
    message: String,
    type: { type: String, default: 'error' }, // 'error', 'success', 'info'
    duration: { type: Number, default: 4000 }
    });

    const emit = defineEmits(['update:modelValue']);

    const icon = computed(() => {
    return props.type === 'success' ? 'check_circle' : 'error_outline';
    });

    const close = () => emit('update:modelValue', false);

    // Fecha automaticamente após X segundos
    watch(() => props.modelValue, (newVal) => {
    if (newVal) {
        setTimeout(close, props.duration);
    }
    });
</script>


<template>
  <v-snackbar
    :model-value="modelValue"
    @update:model-value="val => emit('update:modelValue', val)"
    :timeout="duration"
    :color="type === 'success' ? 'green' : 'red'"
    location="top"
  >
    {{ message }}
    <template #actions>
      <v-btn text @click="close">Fechar</v-btn>
    </template>
  </v-snackbar>
</template>

