<template>
  <div v-if="showModal" class="fixed z-50 inset-0 flex items-center justify-center"
       style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="bg-white rounded-lg w-11/12 md:max-w-md mx-auto shadow-lg overflow-y-auto">
      <div class="p-2">
        <h3 class="text-2xl font-semibold text-gray-700">Delete Dialog</h3>
        <p>Are you sure you want to delete dialog with the user?</p>
        <div class="mt-4 flex justify-end">
          <button class="bg-red-500 text-white rounded-lg px-6 py-2 mr-2 hover:bg-red-700" @click="deleteDialog">Yes</button>
          <button class="bg-gray-700 text-white rounded-lg px-6 py-2 hover:bg-gray-500" @click="closeModal">No</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { EventBus } from "../EventBus";

export default {
  props: ["showModal", 'userToDelete'],
  methods: {
    closeModal() {
      EventBus.emit('close-delete-dialog-modal');
    },
    deleteDialog() {
      let url = `/user/dialog/${this.userToDelete.id}/delete`;

      axios.get(url)
          .then(response => {
            console.log(response);
            window.location.href = "/user/dialog/0";
          })
          .catch(error => {
            console.log(error);
          });
    }
  }
}
</script>