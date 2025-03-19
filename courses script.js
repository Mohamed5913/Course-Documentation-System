// Ensure this script only runs when the DOM is ready.
document.addEventListener('DOMContentLoaded', function() {
    fetchCourses();
});

function fetchCourses() {
    // Use AJAX to request the course list from the server.
    fetch('courses.php')  // The PHP file that will return the list of courses for the user.
        .then(response => response.text())  // Get the response as text (HTML content).
        .then(data => {
            // Find the course list element on the page
            const courseListElement = document.getElementById('course-list');

            // Inject the received HTML content into the course list element.
            courseListElement.innerHTML = data;
        })
        .catch(error => {
            console.error('Error fetching courses:', error);
        });
};

function filterCourses() {
    const searchTerm = document.getElementById("search-courses").value.toLowerCase();
    const courseItems = document.querySelectorAll(".course-item");

    courseItems.forEach(function(course) {
        const courseName = course.querySelector("h3").textContent.toLowerCase();
        if (courseName.includes(searchTerm)) {
            course.style.display = "block";
        } else {
            course.style.display = "none";
        }
    });
}

  // Night Mode Toggle Functionality
const checkbox = document.getElementById("night-mode");
const body = document.body;

// Check local storage for night mode preference
if (localStorage.getItem('night-mode') === 'enabled') {
    body.classList.add("night-mode");
    checkbox.checked = true;
}

// Toggle night mode
checkbox.addEventListener("change", function() {
    if (this.checked) {
        body.classList.add("night-mode");
        localStorage.setItem('night-mode', 'enabled');
    } else {
        body.classList.remove("night-mode");
        localStorage.setItem('night-mode', 'disabled');
    }
});

