<script setup lang="ts">
defineProps<{
  className?: string;
}>();
</script>

<template>
  <img src="/logo_fundo_escuro.svg" alt="Kivvo" :class="className" />
</template> 