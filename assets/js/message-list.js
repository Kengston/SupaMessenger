import { createApp } from 'vue';
import MessageList from './components/MessageList.vue';
import '../styles/app.css'

document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#message-list');

    if (el !== null) {
        const props = {
            selectedUser: JSON.parse(el.dataset.selectedUser),
            currentUser: JSON.parse(el.dataset.currentUser),
            messages: JSON.parse(el.dataset.messages),
            mercureUrl: el.dataset.mercureUrl
        };

        createApp(MessageList, props).mount(el);
    }
});