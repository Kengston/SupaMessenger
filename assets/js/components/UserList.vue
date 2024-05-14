<template>
  <!-- Search Form Starts Here -->
  <form class="max-w-md mx-auto">
    <!-- Search Input -->
    <div class="relative">
      <label for="default-search" class="sr-only">Search</label>
      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" fill="none" viewBox="0 0 20 20">
          <!-- Search Icon -->
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
        </svg>
      </div>
      <!-- Search Input Field -->
      <input type="search" id="default-search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search Users" required />
      <!-- Search Button -->
    </div>
  </form>
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
export default {
  props: ['users', 'currentUser', 'unreadMessageStatusArray'],
  methods: {
    getUserAvatar(user)
    {
      return '/avatars/' + (user.avatarFileName || 'user-tie-solid.svg')
    }
  }
}
</script>