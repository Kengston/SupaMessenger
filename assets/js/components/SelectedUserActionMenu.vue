<template>
  <div id="dropdownSelectedUserActionButton" data-dropdown-toggle="dropdownSelectedUserActionMenu" data-dropdown-placement="bottom" class="w-12 h-12 relative">
    <img class="w-full h-full rounded-full border-4 border-gray-600 transform transition-transform duration-200 ease-in-out hover:scale-110"
         :src="'/avatars/' + (selectedUser.avatarFileName || 'user-tie-solid.svg')"
         :alt="selectedUser.username + '\'s avatar'"
    />
  </div>
  <div id="dropdownSelectedUserActionMenu" class="z-10 hidden bg-white border border-gray-200 rounded-lg shadow-md w-40">
    <ul>
      <li class="flex items-center py-2 px-4 bg-white text-red-500 hover:text-red-700 active:bg-red -200 transition-colors duration-200 cursor-pointer">
        <span class="h-3 w-3 mb-3 mr-2"><i class="fa-solid fa-trash"></i></span>
        <button @click="deleteAction" class="ml-2">Delete dialog</button>
      </li>
    </ul>
  </div>
  <div class="ml-4">
    <h1 class="text-lg font-bold">{{ selectedUser.username }}</h1>
    <span class="message-timestamp text-sm font-normal text-gray-500 dark:text-gray-400 pl-1">
        {{ selectedUser.status === 'online' ? selectedUser.status : `User was here at: ${selectedUser.changeStatusAt}` }}
      </span>
  </div>
  <delete-dialog-modal :showModal="showDeleteDialogModal" :userToDelete="selectedUser"></delete-dialog-modal>
</template>

<script>
import axios from 'axios';
import DeleteDialogModal from "./DeleteDialogModal.vue";
import { EventBus } from "../EventBus";

export default {
  props: ['selectedUser'],
  components: {
    DeleteDialogModal
  },
  data() {
    return {
      showDeleteDialogModal: false,
    };
  },
  created() {
    EventBus.on('close-delete-dialog-modal', this.closeModal);
  },
  beforeDestroy() {
    EventBus.off('close-delete-dialog-modal', this.closeModal);
  },
  methods: {
    closeModal() {
      this.showDeleteDialogModal = false;
    },
    deleteAction() {
      this.showDeleteDialogModal = true;
    }
  }
};
</script>
