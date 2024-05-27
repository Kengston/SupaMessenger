import { createApp } from 'vue';
import MessageForm from './components/MessageForm.vue';
import '../styles/app.css'

document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#message-form');

    if (el !== null) {
        const props = {
            selectedUser: JSON.parse(el.dataset.selectedUser)
        };

        createApp(MessageForm, props).mount(el);
    }
});