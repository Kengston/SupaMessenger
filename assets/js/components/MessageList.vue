<template>
  <div id="message-list" class="flex flex-col h-full">

    <FlashMessage v-bind="flash"/>

    <div class="overflow-y-auto flex-grow p-5 space-y-5" ref="scrollableDiv">
      <!-- Loop over messages -->
      <template v-for="(message, index) in currentMessages" :key="index">
        <!-- Show other user's message -->
        <OtherMessageBubble
            v-if="message.sender === selectedUser.username"
            :message="message"
            :selectedUser="selectedUser"
            :replyToMessage="message.replyToMessage"
            @reply="setReply"
        ></OtherMessageBubble>

        <!-- Show current user's message -->
        <UserMessageBubble
            v-else
            :message="message"
            :currentUser="currentUser"
            :replyToMessage="message.replyToMessage"
            @reply="setReply"
            @enlarge-image="photoToEnlarge = $event"
        ></UserMessageBubble>
      </template>

      <transition
          name="modal"
          enter-active-class="ease-out transition-normal"
          leave-active-class="ease-in transition-fast"
      >
        <div
            v-if="photoToEnlarge"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 w-full h-full"
            @click="photoToEnlarge = null"
        >
          <img
              :src="'/uploads/' + photoToEnlarge"
              alt="Message Photo"
              class="object-contain max-w-[90vw] max-h-[90vh]"
          />
        </div>
      </transition>
    </div>

    <MessageForm
        :selectedUser="selectedUser"
        :replyMessage="replyMessage"
        @cancel-reply="cancelReply"
        @end-reply="replyMessage = null"
    />
  </div>
</template>

<script>
import OtherMessageBubble from './OtherMessageBubble'
import UserMessageBubble from './UserMessageBubble'
import MessageForm from "./MessageForm.vue";
import FlashMessage from "./FlashMessage.vue";
import { EventBus } from "../EventBus";

export default {
  data() {
    return {
      currentMessages: this.messages,
      replyMessage: null,
      flash: { message: null, type: null },
      photoToEnlarge: null
    }
  },
  props: ['selectedUser', 'currentUser', 'mercureUrl', 'messages'],
  mounted() {
    EventBus.on('show-flash', this.showFlash);

    this.scrollToBottom();

    const eventSource = new EventSource(this.mercureUrl);
    eventSource.onmessage = event => {
      const data = JSON.parse(event.data);

      if ('delete' in data) {
        this.currentMessages = this.currentMessages.filter(message => message.id !== data.delete);
      } else if ('edit' in data) {
        const rawMessage = this.currentMessages.find(message => message.id === data.edit);
        if (rawMessage) {
          rawMessage.content = data.editContent;
          rawMessage.updatedAt = data.editTimestamp;
        }
      } else if (data.sender) {
        this.currentMessages.push(data);
      }
    };
  },
  updated() {
    this.scrollToBottom();
  },
  methods: {
    showFlash(message, success) {
      this.flash.message = message;
      this.flash.type = success ? 'alert-success' : 'alert-error';
      setTimeout(() => {
        this.flash.message = null;
      }, 2000);  // Flash message disappears after 2 seconds
    },
    setReply(message) {
      this.replyMessage = message;
    },
    cancelReply() {
      this.replyMessage = null;
    },
    scrollToBottom() {
      this.$nextTick(() => {
        this.$refs.scrollableDiv.scrollTop = this.$refs.scrollableDiv.scrollHeight;
      });
    }
  },
  beforeDestroy() {
    EventBus.off('message-sent', this.showFlash);
  },
  components: {
    OtherMessageBubble,
    UserMessageBubble,
    MessageForm,
    FlashMessage
  },
};
</script>

