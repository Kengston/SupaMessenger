<template>
  <form class="border-t p-5" @submit.prevent="submit">
    <div class="flex items-center justify-center">
      <!-- Emoji button -->
      <button class="flex items-center px-3 bg-gray-200 rounded-lg focus:outline-none"
              type="button" @click="emojiPanel = !emojiPanel">
        <i class="far fa-smile text-gray-600"></i>
      </button>

      <!-- Emoji panel -->
      <div v-if="emojiPanel" class="z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-64 overflow-auto">
        <div class="py-2 text-2xl grid grid-cols-5 gap-2 p-2">
          <button v-for="(emoji, index) in emojis"
                  :key="index" class="emoji-btn focus:outline-none transform transition-transform
                  duration-200 ease-in-out active:scale-110" type="button" @click="addEmojiToMessage(emoji)">
            {{ emoji }}
          </button>
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
  props: ['selectedUser'],
  methods: {
    async submit() {
      let formData = new FormData();
      formData.append('content', this.message);
      formData.append('recipient', this.selectedUser.id);
      if (this.file) {
        formData.append('photoData', this.file);
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
  }
}
</script>