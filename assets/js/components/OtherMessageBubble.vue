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

        <div v-if="message.replyToMessage" class="px-5 py-2 bg-gray-100 text-gray-700 ">
          <div class="flex justify-between items-center">
            <div class="flex items-center">
              <i class="fa-solid fa-reply mr-2 self-center"></i>
              <div class="border-l border-gray-400 pl-2 pr-2">
                <p class="font-semibold">Reply to {{ message.replyToMessage.sender }}:</p>
                <p class="italic">"{{ message.replyToMessage.content }}"</p>
              </div>
            </div>
          </div>
        </div>

        <img v-if="message.photoData" :src="'/uploads/' + message.photoData" alt="Message Photo" class="max-w-xs mt-2" />
        <p class="text-lg font-light max-w-[420px] py-2.5 text-gray-900 dark:text-white break-words overflow-auto">{{ message.content }}</p>
        <span class="message-timestamp text-sm font-normal text-gray-500 dark:text-gray-400">
          <template v-if="message.updatedAt">Edited at {{ message.updatedAt }}</template>
          <template v-else>Delivered</template>
        </span>
      </div>
      <button
          :id="'dropdownMenuIconButton_' + message.id"
          :data-dropdown-toggle="'dropdownDots_' + message.id"
          data-dropdown-placement="bottom-start"
          class="inline-flex self-center items-center p-2 text-sm font-medium text-center text-gray-900 bg-gray-200 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50"
          type="button">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
          <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
        </svg>
      </button>
      <div
          :id="'dropdownDots_' + message.id"
          class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-40 dark:bg-gray-700 dark:divide-gray-600">
        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconButton">
          <li>
            <a @click.prevent="replyToMessage" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Reply</a>
          </li>
          <li class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">
            <button @click="forwardMessage()" class="block">Forward</button>
          </li>
          <li>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Copy</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
  import {Dropdown} from "flowbite";
  import { EventBus } from "../EventBus";

  export default {
    props: {
      message: Object,
      selectedUser: Object,
      replyToMessage: Object
    },
    data() {
      return {
        forwardMessage: false,
        dropdowns: {}, // Storing all instances in `dropdowns` object.
      };
    },
    created() {
      // Initialize your dropdown instance when the component has been created.
      this.dropdowns[this.message.id] = new Dropdown(
          document.getElementById('dropdownDots_' + this.message.id),
          document.getElementById('dropdownMenuIconButton_' + this.message.id)
      );
    },
    methods: {
      forwardMessage() {
        console.log('Emitting event');
        EventBus.emit('show-forward-modal', this.message);
      },
      toggleDropdown(messageId) {
        this.dropdowns[messageId].toggle();
      },
      replyToMessage() {
        this.$emit('reply', this.message);
      },
      copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
          console.log('Text copied to clipboard');
        }).catch(err => {
          console.error('Failed to copy text: ', err);
        });
      },
    },
  }
</script>