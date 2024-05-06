<template>

  <div id="message-list">
    <!-- Loop over messages -->
    <template v-for="(message, index) in messages" :key="index">
      <!-- Show other user's message -->
      <OtherMessageBubble
          v-if="message.sender === selectedUser.username"
          :message="message"
          :selectedUser="selectedUser"
      ></OtherMessageBubble>

      <!-- Show current user's message -->
      <UserMessageBubble
          v-else
          :message="message"
          :currentUser="currentUser"
      ></UserMessageBubble>
    </template>
  </div>
</template>

<script>
import OtherMessageBubble from './OtherMessageBubble'
import UserMessageBubble from './UserMessageBubble'

export default {
  components: {
    OtherMessageBubble,
    UserMessageBubble,
  },
  props: ['selectedUser', 'currentUser', 'messages'],
  created() {
    this.scrollToBottom();
  },
  methods: {
    scrollToBottom() {
      const messageList = this.$el;  // using this.$el gets the DOM element of the component
      messageList.scrollTop = messageList.scrollHeight;
    },

  },
}
</script>

