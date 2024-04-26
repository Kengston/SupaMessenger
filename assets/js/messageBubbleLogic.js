const createUserMessageBubble = (message) => {
    const messageItem = document.createElement('div');
    messageItem.classList.add('message-item', 'py-2', 'px-6', 'rounded-lg', 'max-w-lg', 'flex', 'flex-col', 'space-y-3', 'ml-auto', 'bg-gray-200', 'text-white', 'animate__animated', 'animate__fadeInRight');
    messageItem.setAttribute('data-message-id', message.id);

    const flexContainer = document.createElement('div');
    flexContainer.classList.add('flex', 'items-start', 'gap-2.5');

    const image = document.createElement('img');
    image.classList.add('w-8', 'h-8', 'rounded-full');
    image.setAttribute('src', window.location.origin + '/avatars/' + message.senderAvatar); // Change this line to use senderAvatar from the message
    image.setAttribute('alt', 'User image');

    const messageContentContainer = document.createElement('div');
    messageContentContainer.classList.add('flex', 'flex-col', 'w-full', 'max-w-[420px]', 'leading-1.5', 'p-4', 'border-gray-200', 'bg-gray-100', 'rounded-e-xl', 'rounded-es-xl', 'dark:bg-gray-700');

    const senderInfo = document.createElement('div');
    senderInfo.classList.add('flex', 'items-center', 'space-x-2', 'rtl:space-x-reverse');

    const senderName = document.createElement('span');
    senderName.classList.add('text-sm', 'font-semibold', 'text-gray-900', 'dark:text-white');
    senderName.textContent = message.sender;

    const createdAt = document.createElement('span');
    createdAt.classList.add('text-sm', 'font-normal', 'text-gray-500', 'dark:text-gray-400');
    createdAt.textContent = message.createdAt;

    if (message.photoData) {
        const messagePhoto = document.createElement('img');
        messagePhoto.src = "/uploads/" + message.photoData;
        messagePhoto.alt = "Message Photo";
        messagePhoto.className = "max-w-xs mt-2";
    }

    const messageContent = document.createElement('p');
    messageContent.classList.add('message-content', 'text-lg', 'max-w-[420px]', 'font-light', 'py-2.5', 'text-gray-900', 'dark:text-white');
    messageContent.textContent = message.content;

    const editForm = document.createElement('div');
    editForm.classList.add('message-edit-form', 'hidden');

    const editedAt = document.createElement('span');
    editedAt.classList.add('text-sm', 'font-normal', 'text-gray-500', 'dark:text-gray-400');
    if (message.updatedAt) {
        editedAt.textContent = 'Edited at ' + message.updatedAt;
    } else {
        editedAt.textContent = 'Delivered';
    }

    const dropdownButton = document.createElement('button');
    dropdownButton.id = 'dropdownMenuIconButton_' + message.id;
    dropdownButton.dataset.dropdownToggle = 'dropdownDots_' + message.id;
    dropdownButton.dataset.dropdownPlacement = 'top';
    dropdownButton.classList.add('inline-flex', 'self-center', 'items-center', 'p-2', 'text-sm', 'font-medium', 'text-center', 'text-gray-900', 'bg-gray-200', 'rounded-lg', 'hover:bg-gray-100', 'focus:ring-4', 'focus:outline-none', 'dark:text-white', 'focus:ring-gray-50');
    dropdownButton.type = 'button';

    const dropdownIcon = document.createElement('svg');
    dropdownIcon.classList.add('w-4', 'h-4', 'text-gray-500', 'dark:text-gray-400');
    dropdownIcon.setAttribute('aria-hidden', 'true');
    dropdownIcon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    dropdownIcon.setAttribute('fill', 'currentColor');
    dropdownIcon.setAttribute('viewBox', '0 0 4 15');
    dropdownIcon.innerHTML = '<path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>';

    const dropdownMenu = document.createElement('div');
    dropdownMenu.id = 'dropdownDots_' + message.id;
    dropdownMenu.classList.add('z-10', 'hidden', 'bg-white', 'divide-y', 'divide-gray-100', 'rounded-lg', 'shadow', 'w-40');

    const dropdownList = document.createElement('ul');
    dropdownList.classList.add('py-2', 'text-sm', 'text-gray-700', 'dark:text-gray-200');
    dropdownList.setAttribute('aria-labelledby', 'dropdownMenuIconButton');

    const dropdownItems = [
        { label: 'Reply', href: '#' },
        { label: 'Forward', href: '#' },
        { label: 'Copy', href: '#' },
        { label: 'Edit', href: '#', class: 'message-edit-btn', 'data-message-id': message.id },
        { label: 'Delete', href: 'message/delete/' + message.id }
    ];

    dropdownItems.forEach(item => {
        const listItem = document.createElement('li');
        const anchor = document.createElement('a');
        anchor.classList.add('block', 'px-4', 'py-2', 'hover:bg-gray-100', 'dark:hover:bg-gray-600', 'dark:hover:text-white');
        anchor.textContent = item.label;
        anchor.href = item.href;
        if (item['data-message-id']) {
            anchor.dataset.messageId = item['data-message-id'];
        }
        listItem.appendChild(anchor);
        dropdownList.appendChild(listItem);
    });

    dropdownMenu.appendChild(dropdownList);
    dropdownButton.appendChild(dropdownIcon);
    dropdownButton.appendChild(dropdownMenu);

    senderInfo.appendChild(senderName);
    senderInfo.appendChild(createdAt);
    messageContentContainer.appendChild(senderInfo);
    messageContentContainer.appendChild(messageContent);
    messageContentContainer.appendChild(editForm);
    messageContentContainer.appendChild(editedAt);
    flexContainer.appendChild(image);
    flexContainer.appendChild(messageContentContainer);
    flexContainer.appendChild(dropdownButton);
    messageItem.appendChild(flexContainer);

    return messageItem;
};

const createOtherMessageBubble = (message) => {
    const messageItem = document.createElement('div');
    messageItem.classList.add('message-item', 'py-2', 'pr-6', 'rounded-lg', 'max-w-lg', 'flex', 'flex-col', 'space-y-3', 'mr-auto', 'bg-gray-200', 'text-gray-800', 'animate__animated', 'animate__fadeInLeft');
    messageItem.setAttribute('data-message-id', message.id);

    const flexContainer = document.createElement('div');
    flexContainer.classList.add('flex', 'items-start', 'gap-2.5');

    const image = document.createElement('img');
    image.classList.add('w-8', 'h-8', 'rounded-full');
    image.setAttribute('src', window.location.origin + '/avatars/' + message.senderAvatar); // Change this line to use senderAvatar from the message
    image.setAttribute('alt', 'User image');

    const messageContentContainer = document.createElement('div');
    messageContentContainer.classList.add('flex', 'flex-col', 'w-full', 'max-w-[420px]', 'leading-1.5', 'p-4', 'border-gray-200', 'rounded-e-xl', 'rounded-es-xl', 'bg-gray-100');

    const senderInfo = document.createElement('div');
    senderInfo.classList.add('flex', 'items-center', 'space-x-2', 'rtl:space-x-reverse');

    const senderName = document.createElement('span');
    senderName.classList.add('text-sm', 'font-semibold', 'text-gray-900', 'dark:text-white');
    senderName.textContent = message.sender;

    const createdAt = document.createElement('span');
    createdAt.classList.add('text-sm', 'font-normal', 'text-gray-500', 'dark:text-gray-400');
    createdAt.textContent = message.createdAt;

    const messagePhoto = document.createElement('img');
    if (message.photoData) {
        messagePhoto.src = "/uploads/" + message.photoData;
        messagePhoto.alt = "Message Photo";
        messagePhoto.className = "max-w-xs mt-2";
    } else {
        messagePhoto.src = "";
    }

    const messageContent = document.createElement('p');
    messageContent.classList.add('text-lg', 'font-light', 'py-2.5', 'max-w-[420px]', 'text-gray-900', 'dark:text-white');
    messageContent.textContent = message.content;

    const editedAt = document.createElement('span');
    editedAt.classList.add('text-sm', 'font-normal', 'text-gray-500', 'dark:text-gray-400');
    if (message.updatedAt) {
        editedAt.textContent = 'Edited at ' + message.updatedAt;
    } else {
        editedAt.textContent = 'Delivered';
    }

    senderInfo.appendChild(senderName);
    senderInfo.appendChild(createdAt);
    messageContentContainer.appendChild(senderInfo);
    messageContentContainer.appendChild(messagePhoto);
    messageContentContainer.appendChild(messageContent);
    messageContentContainer.appendChild(editedAt);
    flexContainer.appendChild(image);
    flexContainer.appendChild(messageContentContainer);
    messageItem.appendChild(flexContainer);

    // Adding dropdown menu logic
    const dropdownButton = document.createElement('button');
    dropdownButton.id = 'dropdownMenuIconButton_' + message.id;
    dropdownButton.classList.add('inline-flex', 'self-center', 'items-center', 'p-2', 'text-sm', 'font-medium',
        'text-center', 'text-gray-900', 'bg-gray-200', 'rounded-lg', 'hover:bg-gray-100',
        'focus:ring-4', 'focus:outline-none', 'dark:text-white', 'focus:ring-gray-50');
    dropdownButton.type = 'button';

    // Creating and adding dropdown icon
    const dropdownIcon = document.createElement('svg');
    dropdownIcon.classList.add('h-4', 'text-gray-500', 'dark:text-gray-400');
    dropdownIcon.setAttribute('aria-hidden', 'true');
    dropdownIcon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    dropdownIcon.setAttribute('fill', 'currentColor');
    dropdownIcon.setAttributeNS(null, 'viewBox', '0 0 4 15');
    dropdownIcon.innerHTML = '<i class="fa-solid fa-ellipsis-vertical"></i>';

    dropdownButton.appendChild(dropdownIcon);

    // Creating dropdown menu
    const dropdownMenu = document.createElement('div');
    dropdownMenu.id = 'dropdownDots_' + message.id;
    dropdownMenu.classList.add('z-10', 'hidden', 'bg-white', 'divide-y', 'divide-gray-100', 'rounded-lg', 'shadow', 'w-40');
    dropdownMenu.dataset.popperPlacement = 'bottom';

    dropdownButton.dataset.dropdownToggle = dropdownMenu.id;

    // Creating and adding dropdown items
    const dropdownList = document.createElement('ul');
    dropdownList.classList.add('py-2', 'text-sm', 'text-gray-700', 'dark:text-gray-200');
    dropdownList.setAttribute('aria-labelledby', dropdownButton.id);

    const dropdownItems = [
        { label: 'Reply', href: '#' },
        { label: 'Forward', href: '#' },
        { label: 'Copy', href: '#' },
    ];

    dropdownItems.forEach(item => {
        const listItem = document.createElement('li');
        const anchorLink = document.createElement('a');

        anchorLink.classList.add('block', 'px-4', 'py-2', 'hover:bg-gray-100', 'dark:hover:bg-gray-600', 'dark:hover:text-white');
        anchorLink.textContent = item.label;
        anchorLink.href = item.href;

        listItem.appendChild(anchorLink);
        dropdownList.appendChild(listItem);
    });

    dropdownMenu.appendChild(dropdownList);

    // Adding dropdown button and dropdown menu to the flex container
    flexContainer.appendChild(dropdownButton);
    flexContainer.appendChild(dropdownMenu);

    // Adding flex container to the main message item
    messageItem.appendChild(flexContainer);

    return messageItem;
};

document.addEventListener('click', function(event) {
    // Check if the clicked element is an edit button
    if (event.target.classList.contains('message-edit-btn')) {
        event.preventDefault(); // prevent the default action of clicking a link
        const messageId = event.target.dataset.messageId;
        const messageItem = document.querySelector(`[data-message-id="${messageId}"]`);
        const messageContent = messageItem.querySelector('.message-content');
        const editForm = messageItem.querySelector('.message-edit-form');

        // Toggle visibility between content and the edit form
        messageContent.classList.toggle('hidden');
        editForm.classList.toggle('hidden');

        // Getting the submit and cancel buttons, and the edit input
        let submitButton = editForm.querySelector('.btn-submit');
        let cancelButton = editForm.querySelector('.btn-cancel');
        const editInput = editForm.querySelector('input');

        // Clone the submit and cancel buttons to remove existing event listeners
        const newSubmitButton = submitButton.cloneNode(true);
        submitButton.parentNode.replaceChild(newSubmitButton, submitButton);
        submitButton = newSubmitButton;

        const newCancelButton = cancelButton.cloneNode(true);
        cancelButton.parentNode.replaceChild(newCancelButton, cancelButton);
        cancelButton = newCancelButton;

        // Event listener for the 'Submit' button
        submitButton.addEventListener('click', function(ev) {
            ev.preventDefault();

            const editedContent = editInput.value;
            const messageId = messageItem.dataset.messageId;

            // Send an AJAX request to the Symfony controller endpoint
            fetch(`/user/dialog/message/edit/${messageId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest', // Add this header to indicate an AJAX request
                },
                body: JSON.stringify({ content: editedContent }), // Send the edited content in the request body
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to update message');
                    }
                    // Update the message content and hide the form
                    messageContent.textContent = editedContent;
                    messageContent.classList.remove('hidden');
                    editForm.classList.add('hidden');

                    const messageUpdatedAtElement = messageItem.querySelector('.message-timestamp');
                })
                .catch(error => {
                    console.error('Error updating message:', error);
                });

        });

        // Event listener for the 'Cancel' button
        cancelButton.addEventListener('click', function(ev) {
            ev.preventDefault();

            // Reset the input value and hide the form
            editInput.value = messageContent.textContent;

            messageContent.classList.remove('hidden');
            editForm.classList.add('hidden');
        });
    }
});

document.addEventListener('DOMContentLoaded', (event) => {
    const inputElement = document.querySelector('#message_photoData');
    const iconElement = document.querySelector('.fa-dynamic-icon');

    inputElement.addEventListener('change', function () {
        if (this.files && this.files.length > 0) {
            // A file has been selected
            iconElement.classList.remove('far', 'fa-image', 'text-gray-600');
            iconElement.classList.add('fa-solid', 'fa-xmark');
        } else {
            // No file selected
            iconElement.classList.add('far', 'fa-image', 'text-gray-600');
            iconElement.classList.remove('fa-solid', 'fa-xmark');
        }
    });

    iconElement.addEventListener('click', function () {
        // Check if the icon is in 'cancel' mode
        if (iconElement.classList.contains('fa-xmark')) {
            // Clear the file input
            inputElement.value = '';
            // Change the icon back to the image icon
            iconElement.classList.add('far', 'fa-image', 'text-gray-600');
            iconElement.classList.remove('fa-solid', 'fa-xmark');
        }
    });
});

eventSource.onmessage = function(event) {
    const data = JSON.parse(event.data);
    const messageList = document.getElementById('message-list');

    if ('delete' in data) {
        const messageToDelete = document.querySelector(`[data-message-id="${data.delete}"]`);
        if (messageToDelete) {
            messageToDelete.parentNode.removeChild(messageToDelete);
        }
        return; // Stop further execution since this is a delete command.
    }

    if ('edit' in data) {
        const messageToEdit = document.querySelector(`[data-message-id="${data.edit}"]`);
        if (messageToEdit) {
            const messageContentElement = messageToEdit.querySelector('p.text-sm');
            messageContentElement.textContent = data.editContent;
            const messageUpdatedAtElement = messageToEdit.querySelector('span.message-timestamp');
            messageUpdatedAtElement.textContent = "Edited at " + data.editTimestamp;
        }
        return;
    }

    let newMessage;
    if (data.sender === '{{ currentUser.username }}') {
        newMessage = createUserMessageBubble(data);
    } else {
        newMessage = createOtherMessageBubble(data);
    }

    messageList.appendChild(newMessage);
};