<script setup>
    import '../styles/footer-app.css'
    import { useI18n } from 'vue-i18n'
    const { t } = useI18n()
    const year = new Date().getFullYear()
</script>

<template>
  <v-footer padless height="96" class="footer-root">
    <v-sheet class="footer-sheet" width="100%" elevation="0">
      <v-container>
        <v-row align="center" justify="center">
          <v-col cols="12" class="text-center">
            <v-img
              src="https://i.postimg.cc/9XkMTy8g/97-33-logo-Prancheta-1.png"
              alt="Intelia"
              class="footer-logo"
              contain
              height="32"
            />
            <div class="footer-copy">{{ t('direitos_reservados', { year }) }}</div>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>
  </v-footer>
</template>

