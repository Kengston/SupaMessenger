<!-- UserMessageBubble.vue -->

<template>
  <div class="user-message-item py-2 px-6 rounded-lg max-w-lg flex flex-col space-y-3
    ml-auto bg-gray-200 text-white animate__animated animate__fadeInRight" :data-message-id="message.id">
    <div class="flex items-start gap-2.5">
      <!-- The image src will now use currentUser.avatarFileName (defaulting to `user-tie-solid.svg`) and use currentUser.username for the alt text -->
      <img class="w-8 h-8 rounded-full"
           :src="'/avatars/' + (currentUser.avatarFileName || 'user-tie-solid.svg')"
           :alt="currentUser.username + '\'s avatar'"
      />
      <div class="flex flex-col w-full max-w-[420px] leading-1.5 p-4 border-gray-200 rounded-e-xl rounded-es-xl bg-blue-300">
        <div class="flex items-center space-x-2 rtl:space-x-reverse">
          <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ message.sender }}</span>
          <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ message.createdAt }}</span>
        </div>
        <img v-if="message.photoData" :src="'/uploads/' + message.photoData" alt="Message Photo" class="max-w-xs mt-2" />
        <p class="message-content text-lg max-w-[420px] font-light py-2.5 text-gray-900 dark:text-white">{{ message.content }}</p>
        <span class="message-timestamp text-sm font-normal text-gray-500 dark:text-gray-400">
          <!-- Here you might want to show `read` or `seen` instead. -->
          <template v-if="message.updatedAt">Edited at {{ message.updatedAt }}</template>
          <template v-else>Delivered</template>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
  import { FwbDropdown, FwbListGroup, FwbListGroupItem } from 'flowbite-vue'
  import { initFlowbite } from 'flowbite'
  import {onMounted} from "vue";

  onMounted(() => {
    initFlowbite();
  })

  export default {
    components: {
      FwbDropdown,
      FwbListGroup,
      FwbListGroupItem
    },
    props: {
      message: Object,
      currentUser: Object
    }
  }
</script>