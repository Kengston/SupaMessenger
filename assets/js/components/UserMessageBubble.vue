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

        <div v-if="message.replyToMessage" class="px-5 py-2 bg-blue-300 text-gray-700 ">
          <div class="flex justify-between items-center">
            <div class="flex items-center ">
              <i class="fa-solid fa-reply mr-2 self-center"></i>
              <div class="border-l border-gray-400 pl-2 pr-2">
                <p class="font-semibold">Reply to {{ message.replyToMessage.sender }}:</p>
                <p class="italic">"{{ message.replyToMessage.content }}"</p>
              </div>
            </div>
          </div>
        </div>

        <img v-if="message.photoData" :src="'/uploads/' + message.photoData" alt="Message Photo" class="max-w-xs mt-2" />

        <div v-if="!editMode" class="message-content text-lg max-w-[420px] font-light py-2.5 text-gray-900 dark:text-white">{{ message.content }}</div>


        <!-- Edit mode -->
        <div v-if="editMode" class="flex items-center">
          <input class="message-content-form max-w-[420px] font-light text-gray-900 bg-white border border-gray-200 rounded p-2 mr-1" v-model="editInputContent" type="text">

          <button @click="submitEdit" class="btn-submit bg-white text-black border border-gray-200 rounded p-2 mr-1">
            <i class="fa-solid fa-check"></i>
          </button>

          <button @click="cancelEdit" class="btn-cancel bg-white text-black border border-gray-200 rounded p-2">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
        <!-- End of Edit mode -->

        <span class="message-timestamp text-sm font-normal text-gray-500 dark:text-gray-400">
          <template v-if="message.updatedAt">Edited at {{ message.updatedAt }}</template>
          <template v-else>Delivered</template>
        </span>
      </div>

      <!-- Dropdown menu -->
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
          <li>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Forward</a>
          </li>
          <li>
            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Copy</a>
          </li>
          <li class="hover:bg-gray-100">
            <button class="message-edit-btn block px-4 py-2" @click="toggleEdit">Edit</button>
          </li>
          <li>
            <a
                :href="'message/delete/' + message.id"
                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
            >
              Delete
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { Dropdown } from 'flowbite';

export default {
  props: {
    message: Object,
    currentUser: Object,
    replyToMessage: Object
  },
  data() {
    return {
      editMode: false,
      editInputContent: "",
      dropdowns: {}, // Storing all instances in `dropdowns` object.
    };
  },
  mounted() {
    this.dropdowns[this.message.id] = new Dropdown(
        document.getElementById('dropdownDots_' + this.message.id),
        document.getElementById('dropdownMenuIconButton_' + this.message.id)
    );
  },

  methods: {
    toggleDropdown(messageId) {
      this.dropdowns[messageId].toggle();
    },
    toggleEdit() {
      this.editMode = !this.editMode;
      this.editInputContent = this.message.content; // Copy the content to editInputContent when entering edit mode
    },
    submitEdit() {
      // Process the submission here
      this.editMessage();
    },
    cancelEdit() {
      // Cancel the edit here
      this.editMode = false;
    },
    editMessage() {
      // Use current value of editInputContent
      const editedContent = this.editInputContent;
      const messageId = this.message.id;

      fetch(`/user/dialog/message/edit/${messageId}`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          content: editedContent
        })
      })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // If successful, update message.content to reflect the change
              this.message.content = editedContent;
              this.editMode = false; // Exit the edit mode
            } else {
              throw new Error(data.error);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
    },
    replyToMessage() {
      this.$emit('reply', this.message);
    }
  },
};
</script>