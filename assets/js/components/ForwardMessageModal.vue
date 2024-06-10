<template>
  <div
      v-if="showModal"
      class="fixed z-50 inset-0 flex items-center justify-center"
      style="background-color: rgba(0, 0, 0, 0.5);"
  >
    <div class="bg-white rounded-lg w-11/12 md:max-w-md mx-auto shadow-lg overflow-y-auto">
      <div class="flex justify-between items-center p-6">
        <h3 class="text-2xl font-semibold text-gray-700">Forward message to:</h3>
        <button @click="closeModal">
          <i class="fas fa-times text-gray-700 hover:text-gray-500 transition-colors"></i>
        </button>
      </div>

      <div class="px-6 mb-4 border-b"></div>

      <ul class="max-h-64 overflow-y-auto">
        <li
            v-for="user in users"
            :key="user.id"
            class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 cursor-pointer"
            @click.stop="forwardMessage(user)"
        >
          <div class="flex items-center space-x-4">
            <img
                class="w-10 h-10 rounded-full object-cover border-2 border-gray-200"
                :src="getUserAvatar(user)"
                :alt="user.username"
            >
            <div>
              <h2 class="text-lg font-semibold text-gray-700">{{ user.username }}</h2>
              <p class="text-sm text-gray-500">Click to select</p>
            </div>
          </div>
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
      this.showModal = false; // Close the modal after forwarding the message
      // After successful action you may close the modal
      this.closeModal();
    },
    getUserAvatar(user) {
      return '/avatars/' + (user.avatarFileName || 'user-tie-solid.svg')
    },
  }
};
</script>