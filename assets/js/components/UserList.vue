
<template>
  <!-- Search Form Starts Here -->
  <div>
    <div class="m-2 relative flex items-center">
    <span class="absolute left-3 top-1/2 transform -translate-y-1/2">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4 text-gray-400">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
    </span>
      <input v-model="searchInput" @input="searchUsers" type="search" placeholder="Search Users" class="pl-10 outline-none rounded px-6 py-3 w-full text-sm focus:border-blue-500 border border-l-0 border-gray-200 required" />
    </div>
    <div v-if="searchResults.length > 0">
      <ul class="bg-white w-3/12 mx-auto block border border-gray-100 rounded shadow-lg z-50 absolute">
        <UserItem
            v-for="result in searchResults"
            :key="result.id"
            :userData="result"
            :unreadMessageStatusArray="unreadMessageStatusArray"
            :lastMessagesInDialogArray="lastMessagesInDialogArray"
        />
      </ul>
    </div>
  </div>
  <ul class="mt-5 space-y-5">
    <!-- Iterate throught filteredUsers -->
    <UserItem
        v-for="user in filteredUsers"
        :key="user.id"
        :userData="user"
        :unreadMessageStatusArray="unreadMessageStatusArray"
        :lastMessagesInDialogArray="lastMessagesInDialogArray"
    />
  </ul>

  <ForwardMessageModal :showModal="showForwardMessageModal" :message="forwardMessage" :users="users"/>
</template>

<script>
import axios from "axios";
import UserItem from "./UserItem.vue";
import ForwardMessageModal from "./ForwardMessageModal.vue";
import {EventBus} from "../EventBus";
export default {
  components: {ForwardMessageModal, UserItem},
  data() {
    return {
      searchInput: "",
      searchResults: [],
      showForwardMessageModal: false,
      forwardMessage: null,
    };
  },
  props: ['users', 'currentUser', 'unreadMessageStatusArray', 'lastMessagesInDialogArray'],
  created() {
    // Listen to the event
    EventBus.on('show-forward-modal', (message) => {
      console.log('Event received');
      this.forwardMessage = message;
      this.showForwardMessageModal = true;
    });
  },
  computed: {
    filteredUsers() {
      return this.users.filter(user =>
          this.lastMessagesInDialogArray.hasOwnProperty(user.id) &&
          this.lastMessagesInDialogArray[user.id]
      );
    }
  },

  methods: {
    searchUsers() {
      if (!this.searchInput) {
        this.searchResults = [];
        return;
      }

      // client side filtering
      this.searchResults = this.users.filter(user =>
          user.username.toLowerCase().includes(this.searchInput.toLowerCase())
      );
    },
    selectUser(user) {
      // currently this method does nothing
      // this.searchInput = user.username;
      // this.searchResults = [];
    },
    getUserAvatar(user)
    {
      return '/avatars/' + (user.avatarFileName || 'user-tie-solid.svg')
    }
  }
}
</script>