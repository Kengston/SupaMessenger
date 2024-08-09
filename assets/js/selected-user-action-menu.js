import { createApp } from 'vue';
import SelectedUserActionMenu from './components/SelectedUserActionMenu.vue';
import '../styles/app.css'

document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#selected-user-action-menu');

    if (el !== null) {
        const props = {
            selectedUser: JSON.parse(el.dataset.selectedUser),
        };

        createApp(SelectedUserActionMenu, props).mount(el);
    }
});