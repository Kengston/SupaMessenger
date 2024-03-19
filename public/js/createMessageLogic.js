const createUserMessageBubble = (message) => {
    const messageItem = document.createElement('div');
    messageItem.classList.add('message-item', 'py-2', 'px-6', 'rounded-lg', 'max-w-lg', 'flex', 'flex-col', 'space-y-3', 'mr-auto', 'bg-gray-200', 'text-white', 'animate__animated', 'animate__fadeInRight');
    messageItem.setAttribute('data-message-id', message.id);

    const flexContainer = document.createElement('div');
    flexContainer.classList.add('flex', 'items-start', 'gap-2.5');

    const image = document.createElement('img');
    image.classList.add('w-8', 'h-8', 'rounded-full');
    image.setAttribute('src', 'https://static.vecteezy.com/system/resources/previews/002/592/204/original/little-student-afro-boy-avatar-character-free-vector.jpg');
    image.setAttribute('alt', 'User image');

    const messageContentContainer = document.createElement('div');
    messageContentContainer.classList.add('flex', 'flex-col', 'w-full', 'max-w-[320px]', 'leading-1.5', 'p-4', 'border-gray-200', 'bg-gray-100', 'rounded-e-xl', 'rounded-es-xl', 'dark:bg-gray-700');

    const senderInfo = document.createElement('div');
    senderInfo.classList.add('flex', 'items-center', 'space-x-2', 'rtl:space-x-reverse');

    const senderName = document.createElement('span');
    senderName.classList.add('text-sm', 'font-semibold', 'text-gray-900', 'dark:text-white');
    senderName.textContent = message.sender;

    const createdAt = document.createElement('span');
    createdAt.classList.add('text-sm', 'font-normal', 'text-gray-500', 'dark:text-gray-400');
    createdAt.textContent = message.createdAt;

    const messageContent = document.createElement('p');
    messageContent.classList.add('message-content', 'text-sm', 'font-normal', 'py-2.5', 'text-gray-900', 'dark:text-white');
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
    messageItem.classList.add('message-item', 'py-2', 'pr-6', 'pl-24', 'rounded-lg', 'max-w-lg', 'flex', 'flex-col', 'space-y-3', 'ml-auto', 'bg-gray-200', 'text-gray-800', 'animate__animated', 'animate__fadeInLeft');
    messageItem.setAttribute('data-message-id', message.id);

    const flexContainer = document.createElement('div');
    flexContainer.classList.add('flex', 'items-start', 'gap-2.5');

    const image = document.createElement('img');
    image.classList.add('w-8', 'h-8', 'rounded-full');
    image.setAttribute('src', 'https://i.pinimg.com/736x/45/6a/e4/456ae4e5ae1e340ee824209fd8874c8b.jpg');
    image.setAttribute('alt', 'User image');

    const messageContentContainer = document.createElement('div');
    messageContentContainer.classList.add('flex', 'flex-col', 'w-full', 'max-w-[320px]', 'leading-1.5', 'p-4', 'border-gray-200', 'rounded-e-xl', 'rounded-es-xl', 'bg-blue-300');

    const senderInfo = document.createElement('div');
    senderInfo.classList.add('flex', 'items-center', 'space-x-2', 'rtl:space-x-reverse');

    const senderName = document.createElement('span');
    senderName.classList.add('text-sm', 'font-semibold', 'text-gray-900', 'dark:text-white');
    senderName.textContent = message.sender;

    const createdAt = document.createElement('span');
    createdAt.classList.add('text-sm', 'font-normal', 'text-gray-500', 'dark:text-gray-400');
    createdAt.textContent = message.createdAt;

    const messageContent = document.createElement('p');
    messageContent.classList.add('text-sm', 'font-normal', 'py-2.5', 'text-gray-900', 'dark:text-white');
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