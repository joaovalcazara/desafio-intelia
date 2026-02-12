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
  <Transition name="toast">
    <div v-if="modelValue" class="toast-container" :class="type">
      <div class="toast-content">
        <span class="message">{{ message }}</span>
      </div>
      <button class="close-btn" @click="close">&times;</button>
    </div>
  </Transition>
</template>

