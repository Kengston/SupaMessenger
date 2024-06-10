<template>
  <div
      v-if="showModal"
      class="fixed z-50 inset-0 flex items-center justify-center"
      style="background-color: rgba(0, 0, 0, 0.5);"
  >
    <div class="bg-white rounded-lg w-11/12 md:max-w-md mx-auto shadow-lg overflow-y-auto">
      <div class="flex justify-between items-center p-2">
        <h3 class="text-2xl pl-1.5 font-semibold text-gray-700">Forward message to:</h3>
        <button @click="closeModal">
          <i class="fas fa-times text-gray-700 hover:text-gray-500 transition-colors"></i>
        </button>
      </div>
      <div class="m-2 relative flex items-center">
        <span class="absolute left-3 top-1/2 transform -translate-y-1/2">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4 text-gray-400">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </span>
        <input v-model="searchInput" @input="searchUsers" type="search" placeholder="Search Users" class="pl-10 outline-none rounded px-6 py-3 w-full text-sm focus:border-blue-500 border border-l-0 border-gray-200 required" />
      </div>

      <div class="px-6 mb-4 border-b"></div>

      <div v-if="displayedUsers.length > 0">
        <ul class="max-h-72 overflow-y-auto">
          <li
              v-for="user in displayedUsers"
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
  </div>
</template>

<script>
export default {
  props: ['showModal', 'message', 'users', 'lastMessagesInDialogArray'],
  data() {
    return {
      searchInput: "",
      searchResults: [],
    };
  },
  computed: {
    filteredUsers() {
      return this.users.filter(user =>
          this.lastMessagesInDialogArray.hasOwnProperty(user.id) &&
          this.lastMessagesInDialogArray[user.id]
      );
    },
    displayedUsers() {
      return this.searchResults.length > 0 ? this.searchResults : this.filteredUsers;
    }
  },
  methods: {
    searchUsers() {
      if (!this.searchInput) {
        this.searchResults = [];
        return;
      }

      // client side filtering
      this.searchResults = this.filteredUsers.filter(user =>
          user.username.toLowerCase().includes(this.searchInput.toLowerCase())
      );
    },
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