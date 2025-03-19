document.addEventListener('DOMContentLoaded', () => {
    // Load selected course details from localStorage
    const course = JSON.parse(localStorage.getItem('selectedCourse'));
    if (course) {
        document.getElementById('course-name').textContent = course.name;
        document.getElementById('course-description').textContent = course.description;
        document.getElementById('course-instructor').textContent = course.instructor;
    }

    // Media attachments logic
    const mediaContainer = document.getElementById('media-container');
    const uploadForm = document.getElementById('upload-form');
    const mediaInput = document.getElementById('media-input');

    // Load existing media from localStorage
    const mediaKey = `media-${course.name}`;
    const mediaFiles = JSON.parse(localStorage.getItem(mediaKey)) || [];
    mediaFiles.forEach((file) => {
        const img = document.createElement('img');
        img.src = file;
        img.alt = `Media for ${course.name}`;
        img.style.maxWidth = '200px';
        mediaContainer.appendChild(img);
    });

    // Handle media uploads
    uploadForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const file = mediaInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const fileURL = e.target.result;

                // Save to localStorage
                mediaFiles.push(fileURL);
                localStorage.setItem(mediaKey, JSON.stringify(mediaFiles));

                // Display the uploaded image
                const img = document.createElement('img');
                img.src = fileURL;
                img.alt = `Media for ${course.name}`;
                img.style.maxWidth = '200px';
                mediaContainer.appendChild(img);

                // Reset the input
                mediaInput.value = '';
            };
            reader.readAsDataURL(file);
        }
    });

    // Night mode functionality
    const checkbox = document.querySelector('.checkbox');
    const body = document.body;

    // Load the saved theme from localStorage, if any
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        body.classList.add('night');
        checkbox.checked = true; // Keep checkbox checked in dark mode
    } else {
        body.classList.remove('night');
        checkbox.checked = false; // Ensure checkbox is unchecked in light mode
    }

    // Toggle theme on checkbox change
    checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
            body.classList.add('night');
            localStorage.setItem('theme', 'dark'); // Save the selected theme in localStorage
        } else {
            body.classList.remove('night');
            localStorage.setItem('theme', 'light'); // Save the selected theme in localStorage
        }
    });
});
