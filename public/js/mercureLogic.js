const eventSource = new EventSource(userApiEndpoint);

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
            const contentElement = messageToEdit.querySelector('p.text-sm');
            contentElement.textContent = data.editContent;
        }
        return;
    }

    let newMessage;
    if (data.sender === currentUsername) {
        newMessage = createUserMessageBubble(data);
    } else {
        newMessage = createOtherMessageBubble(data);
    }

    messageList.appendChild(newMessage);
};

window.onload = function() {
    const messageList = document.getElementById('message-list');
    messageList.scrollTop = messageList.scrollHeight;
};