<template>
  <li
      class="animate__animated animate__fadeIn">
    <a class="block py-2 px-6 hover:bg-gray-200 rounded-lg flex items-center"
       :href="'/user/dialog/' + userData.id">
      <div class="w-8 h-8 mr-3">
        <img class="w-full h-full rounded-full border border-gray-600"
             :src="getUserAvatar(userData)"
             :alt="userData.username">
      </div>
      <div>
        <h1 class="text-lg font-bold">
          {{ userData.username }}
          <span
              v-if="unreadMessageStatusArray[userData.id]"
              class="w-2 h-2 ml-2 inline-block bg-blue-500 rounded-full"
          />
        </h1>
        <p class="text-sm text-gray-500">
          {{ showLastMessage(userData.id) }}
        </p>
      </div>
    </a>
  </li>
</template>

<script>
export default {
  props: ['userData', 'unreadMessageStatusArray', 'lastMessagesInDialogArray'],
  methods: {
    getUserAvatar(user) {
      return '/avatars/' + (user.avatarFileName || 'user-tie-solid.svg')
    },
    showLastMessage(userId) {
      if (this.lastMessagesInDialogArray.hasOwnProperty(userId) && this.lastMessagesInDialogArray[userId]) {
        if (this.lastMessagesInDialogArray[userId].length > 30) {
          return this.lastMessagesInDialogArray[userId].slice(0, 30) + '...'
        } else {
          return this.lastMessagesInDialogArray[userId]
        }
      } else {
        return 'No recent message!'
      }
    }
  },
}
</script>