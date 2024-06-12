<template>
  <transition
      enter-active-class="transition-opacity duration-500"
      leave-active-class="transition-opacity duration-500"
      enter-class="opacity-0"
      leave-to-class="opacity-0"
  >
    <div v-if="message" class="alert flex items-center justify-between p-4 mb-5 bg-green-100 text-green-700 rounded-lg border border-green-300" v-bind:class="type">
      <div>Message Successfully Sent!</div>
      <button @click="closeMessage" class="text-gray-500 hover:text-gray-700 transition-colors">
        <i class="fas fa-times "></i>
      </button>
    </div>
  </transition>
</template>

<script>
export default {
  props: ['message', 'type'],
  methods: {
    closeMessage() {
      this.$emit('update:message', null);
    }
  }
};
</script>