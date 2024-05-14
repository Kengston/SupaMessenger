<template>

  <div id="message-list">
    <!-- Loop over messages -->
    <template v-for="(message, index) in currentMessages" :key="index">
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
  data() {
    return {
      currentMessages: this.messages
    }
  },
  props: ['selectedUser', 'currentUser', 'mercureUrl', 'messages'],
  mounted() {
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
  methods: {

  },
  components: {
    OtherMessageBubble,
    UserMessageBubble
  },
};
</script>

