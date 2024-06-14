<template>
  <div>
    <div v-if="replyMessage" class="px-5 py-2 mb-4 border rounded bg-gray-200 text-gray-700 relative">
      <div class="flex justify-between items-center">
        <div class="flex items-center ml-16">
          <i class="fa-solid fa-reply mr-2 self-center"></i>
          <div class="border-l border-gray-400 pl-2 pr-2">
            <p class="font-semibold">Reply to {{ replyMessage.sender }}:</p>
            <p class="italic">"{{ replyMessage.content }}"</p>
          </div>
        </div>
        <!-- The close button -->
        <div class="border-l border-gray-400 pl-2 pr-8 py-2">
          <button class="w-6 h-6 flex items-center justify-center border-2 border-gray-900 rounded-full" @click="cancelReply">
            <i class="fa-solid fa-xmark text-gray-900"></i>
          </button>
        </div>
      </div>
    </div>
    <form class="border-t pl-5 pr-5" @submit.prevent="submit">
      <div class="relative flex items-center justify-center">

          <!-- Emoji button -->
        <div class="relative">
        <!-- Emoji button -->
          <button class="flex items-center px-3 bg-gray-200 rounded-lg focus:outline-none" type="button" @click="emojiPanel = !emojiPanel">
            <i class="far fa-smile text-gray-600"></i>
          </button>

          <!-- Emoji panel -->
          <div v-if="emojiPanel" class="absolute  bottom-full mb-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-64 max-h-64 overflow-y-auto">
            <div class="py-2 text-2xl grid grid-cols-5 gap-2 p-2">
              <button v-for="(emoji, index) in emojis"
                      :key="index" class="emoji-btn focus:outline-none transform transition-transform
                    duration-200 ease-in-out active:scale-110" type="button" @click="addEmojiToMessage(emoji)">
                {{ emoji }}
              </button>
            </div>
          </div>
        </div>

        <!-- Image button -->
        <label class="flex items-center px-3 bg-gray-200 rounded-lg focus:outline-none" @change="uploadImage">
          <input type="file" id="message_photoData" ref="fileInput" @change="uploadImage" class="w-0 h-0 opacity-0 overflow-hidden absolute">
          <i class="far fa-image text-gray-600 fa-dynamic-icon"></i>
        </label>

        <!-- Input field (input) -->
        <input class="form-textarea flex-grow rounded-lg py-2 px-12 mx-2 resize-vertical" v-model="message"/>

        <!-- Submit button -->
        <button class="px-5 py-2 bg-blue-500 text-white rounded-lg" type="submit">
          <i class="far fa-paper-plane"></i>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios';  // you need to import axios

export default {
  data() {
    return {
      emojis: ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '😎', '😍', '😒'],
      emojiPanel: false,
      message: '',
      file: null
    }
  },
  props: ['selectedUser', 'replyMessage'],
  methods: {
    async submit() {
      let formData = new FormData();
      formData.append('content', this.message);
      formData.append('recipient', this.selectedUser.id);
      if (this.file) {
        formData.append('photoData', this.file);
      }
      if (this.replyMessage) {
        formData.append('replyToMessageId', this.replyMessage.id);
      }

      try {
        const response = await axios.post('/user/dialog/message/new', formData);
        if (response.data.success) {
          this.message='';
          if (this.file) {
            this.file = null;
            this.$refs.fileInput.value = '';
          }
          this.$emit('message-sent', response.data.message);

          if (this.replyMessage) {
            this.$emit('end-reply');
          }
        } else {
          console.error(response.data.error);
        }
      } catch (error) {
        console.error(error);
      }
    },
    uploadImage(event) {
      this.file = event.target.files[0];
    },
    addEmojiToMessage(emoji) {
      this.message += emoji;
    },
    cancelReply() {
      this.$emit('cancel-reply');
    }
  }
}
</script>