document.addEventListener('DOMContentLoaded', () => {
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
  