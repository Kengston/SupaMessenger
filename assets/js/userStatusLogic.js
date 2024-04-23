document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dropdownUserStatusIconButton').addEventListener('click', function() {
        let iconElementNormal = document.getElementById('iconNormal');
        let iconElementAnimated = document.getElementById('iconAnimated');
        iconElementNormal.classList.toggle('hidden');
        iconElementAnimated.classList.toggle('hidden');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('dropdownUserStatusIconMenu').addEventListener('click', function(event) {
        let target = event.target;
        if (target.tagName === 'LI') {
            let status = target.getAttribute('data-status');
            let statusDot = document.getElementById('statusDot');
            let statusText = document.getElementById('statusText');
            let dropdownMenu = document.getElementById('dropdownUserStatusIconMenu');
            let iconElementNormal = document.getElementById('iconNormal');
            let iconElementAnimated = document.getElementById('iconAnimated');

            // Update status dot color
            if (status === 'Online') {
                statusDot.classList.remove('bg-orange-400', 'bg-red-400');
                statusDot.classList.add('bg-green-500');
            } else if (status === 'Busy') {
                statusDot.classList.remove('bg-green-500', 'bg-red-400');
                statusDot.classList.add('bg-orange-400');
            } else if (status === 'Offline') {
                statusDot.classList.remove('bg-green-500', 'bg-orange-400');
                statusDot.classList.add('bg-red-400');
            }

            // Update status text
            statusText.textContent = status;

            // Close the dropdown menu
            dropdownMenu.classList.add('hidden');

            // Show iconElementNormal and hide iconElementAnimated
            iconElementNormal.classList.remove('hidden');
            iconElementAnimated.classList.add('hidden');
        }
    });
});