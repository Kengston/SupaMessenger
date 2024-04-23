document.addEventListener('DOMContentLoaded', function() {
    const emojiMenu = document.getElementById('dropdownMenuEmoji');
    const messageContentInput = document.getElementById('message_content');

    // Add event listener to each emoji button
    const emojiButtons = document.querySelectorAll('.emoji-btn');
    emojiButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Get the emoji from the clicked button
            const emoji = button.textContent;

            // Insert the emoji into the message form content input field
            if (messageContentInput) {
                messageContentInput.value += emoji;

                console.log('Message content:', messageContentInput.value); // Debugging
            } else {
                console.error('Message content input field not found.'); // Debugging
            }

        });
    });
});

