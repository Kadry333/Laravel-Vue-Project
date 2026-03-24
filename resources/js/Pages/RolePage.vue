<script setup>
defineProps({
  role: String
})
</script>

<template>
  <div style="display:flex; justify-content:center; align-items:center; height:100vh;">
    <h1>You are {{ role }}</h1>
  </div>
</template>