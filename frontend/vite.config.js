import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    host: '0.0.0.0', // para o nginx acessar o Vite
    port: 5173,
    watch: {
      usePolling: true, 
    },
  },
})