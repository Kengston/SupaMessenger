<template>
  <!-- Search Form Starts Here -->
  <div>
    <div class="mb-3 relative">
      <span class="px-3 py-2 leading-tight bg-white rounded-l text-sm border border-r-0 border-gray-200 cursor-not-allowed">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4 text-gray-400 mx-auto">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </span>
      <input v-model="searchInput" @input="searchUsers" type="search" placeholder="Search Users" class="pl-10 outline-none rounded rounded-l px-6 py-3 w-full text-sm focus:border-blue-500 border border-l-0 border-gray-200 required" />
      <div v-if="searchResults.length > 0">
        <ul class="bg-white w-full border border-gray-100 rounded shadow-lg z-50 absolute">
          <li v-for="result in searchResults" @click="selectUser(result)" :key="result.id" class="p-3 cursor-pointer hover:bg-gray-50">
            {{ result.username }}
          </li>
        </ul>
      </div>
    </div>
  </div>
  <ul class="mt-5 space-y-5">
    <!-- Iterate throught users -->
    <li
      v-for="user in users"
      :key="user.id"
      class="animate__animated animate__fadeIn">
      <a class="block py-2 px-6 hover:bg-gray-200 rounded-lg flex items-center"
         :href="'/user/dialog/' + user.id">
        <div class="w-8 h-8 mr-3">
          <img class="w-full h-full rounded-full border border-gray-600"
               :src="getUserAvatar(user)"
               :alt="user.username">
        </div>
        <div>
          <h1 class="text-lg font-bold">
            {{ user.username }}
            <span v-if="unreadMessageStatusArray[user.id]" class="w-2 h-2 ml-2 inline-block bg-blue-500 rounded-full"></span>
          </h1>
          <p class="text-sm text-gray-500">
            {{ user.lastMessageContent || 'No recent message!' }}
          </p>
        </div>
      </a>
    </li>
  </ul>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      searchInput: "",
      searchResults: []
    };
  },
  props: ['users', 'currentUser', 'unreadMessageStatusArray'],
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