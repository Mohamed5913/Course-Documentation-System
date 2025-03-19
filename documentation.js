document.addEventListener('DOMContentLoaded', () => {
    const uploadForm = document.getElementById('upload-form');
    const fileUpload = document.getElementById('file-upload');
    const fileList = document.getElementById('file-list');

    // Handle file upload
    uploadForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const uploadedFile = fileUpload.files[0];
        if (uploadedFile) {
            const listItem = document.createElement('li');
            listItem.textContent = uploadedFile.name;
            fileList.appendChild(listItem);

            const successMessage = document.createElement('p');
            successMessage.textContent = `File "${uploadedFile.name}" uploaded successfully!`;
            successMessage.style.color = 'green';
            fileList.appendChild(successMessage);

        } else {
            alert('Please select a file to upload.');
        }

        uploadForm.reset();
    });
});
