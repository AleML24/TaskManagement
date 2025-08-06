<script setup>
import TaskList from './components/TaskList.vue';
import NotificationToast from './components/NotificationToast.vue';
</script>

<template>
  <div class="main">
    <task-list />
    <notification-toast></notification-toast>
  </div>
</template>

<style scoped>
.main {
  display: flex;
  justify-content: center;
  justify-items: center;
  width: 100dvw;
  height: 100dvh;
}
</style>
