<!-- OtherMessageBubble.vue -->

<template>
  <div class="other-message-item py-2 pr-6 rounded-lg max-w-lg flex flex-col space-y-3
    mr-auto  bg-gray-200 text-gray-800 animate__animated animate__fadeInLeft" :data-message-id="message.id">
    <div class="flex items-start gap-2.5">
      <img class="w-8 h-8 rounded-full"
           :src="'/avatars/' + (selectedUser.avatarFileName || 'user-tie-solid.svg')"
           :alt="selectedUser.username + '\'s avatar'"
      />
      <div class="flex flex-col w-full max-w-[420px] leading-1.5 p-4 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl dark:bg-gray-700">
        <div class="flex items-center space-x-2 rtl:space-x-reverse">
          <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ message.sender }}</span>
          <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ message.createdAt }}</span>
        </div>
        <img v-if="message.photoData" :src="'/uploads/' + message.photoData" alt="Message Photo" class="max-w-xs mt-2" />
        <p class="text-lg font-light max-w-[420px] py-2.5 text-gray-900 dark:text-white">{{ message.content }}</p>
        <span class="message-timestamp text-sm font-normal text-gray-500 dark:text-gray-400">
          <template v-if="message.updatedAt">Edited at {{ message.updatedAt }}</template>
          <template v-else>Delivered</template>
        </span>
      </div>

    </div>
  </div>
</template>

<script>
import { FwbDropdown, FwbListGroup, FwbListGroupItem } from 'flowbite-vue'

export default {
  components: {
    FwbDropdown,
    FwbListGroup,
    FwbListGroupItem
  },
  props: {
    message: Object,
    selectedUser: Object
  }
}
</script>