import { createApp } from "vue";
import UserList from './components/UserList.vue';
import '../styles/app.css';

document.addEventListener('DOMContentLoaded', function () {

    const el = document.querySelector('#user-list');

    if (el !== null) {

        const props = {
            users: JSON.parse(el.dataset.users),
            currentUser: JSON.parse(el.dataset.currentUser),
            unreadMessageStatusArray: JSON.parse(el.dataset.unreadMessageStatusArray),
            lastMessagesInDialogArray: JSON.parse(el.dataset.lastMessagesInDialogArray),
            lastMessageTimeInDialogArray: JSON.parse(el.dataset.lastMessageTimeInDialogArray),
            mercureUrl: el.dataset.mercureUrl
        };

        createApp(UserList, props).mount(el);
    }
})