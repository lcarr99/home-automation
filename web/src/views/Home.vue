<template>
  <div class="grid">
    <div>
      <article><strong>Income:</strong> £100</article>
    </div>
    <div>
      <article><strong>Expenditure:</strong> £100</article>
    </div>
    <div>
      <article><strong>Savings:</strong> £1000</article>
    </div>
  </div>
</template>

<script setup>
import login from "./Login.vue";
import { useUserStore } from "../stores/user";

const userStore = useUserStore();
</script>

<style scoped></style>
