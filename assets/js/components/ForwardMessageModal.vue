<template>
  <div v-if="showModal" class="fixed z-10 inset-0 overflow-y-auto bg-gray-500 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded pt-5 pb-4 px-4 w-full max-w-lg">
      <div class="flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Forward Message</h3>
        <button @click="closeModal">
          X
        </button>
      </div>

      <div class="my-4">
        <p>Message to forward: "{{ message }}"</p>
      </div>

      <ul>
        <!-- Assuming each user object has a name property -->
        <!-- You will need to replace it according to your scenario -->
        <li v-for="(user, index) in users" :key="index">
          <button @click="forwardMessage(user)">
            {{ user.username }}
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: ['showModal', 'message', 'users'],
  methods: {
    closeModal() {
      this.$emit('closeModal');
    },
    forwardMessage(user) {
      console.log(`Message "${this.message}" forwarded to user:`, user);
      // Add your logic here to perform the forward action
      // After successful action you may close the modal
      this.closeModal();
    }
  }
};
</script>