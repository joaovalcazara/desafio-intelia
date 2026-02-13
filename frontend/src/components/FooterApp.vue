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
            <div class="footer-copy">© 2024 Todos os direitos reservados</div>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>
  </v-footer>
</template>

<script setup>
// No props required; static footer
</script>

<style scoped>
.footer-root {
  background-color: #00172c;
  color: #ffffff;
}
.footer-sheet {
  background-color: transparent;
}
.footer-logo {
  margin-bottom: 6px;
}
.footer-copy {
  color: #e0e0e0;
  font-size: 0.95rem;
}
</style>
