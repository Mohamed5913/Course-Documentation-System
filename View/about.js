document.addEventListener('DOMContentLoaded', () => {
    // Smooth Scrolling
    const navLinks = document.querySelectorAll('.nav-link');
  
    navLinks.forEach(link => {
      link.addEventListener('click', event => {
        const targetId = link.getAttribute('href').split('#')[1];
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
          event.preventDefault();
          targetSection.scrollIntoView({ behavior: 'smooth' });
        }
      });
    });
  
    // Fade-In Animation on Scroll
    const sections = document.querySelectorAll('section');
  
    const fadeInOnScroll = () => {
      sections.forEach(section => {
        const sectionTop = section.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        if (sectionTop < windowHeight - 100) {
          section.classList.add('visible');
        } else {
          section.classList.remove('visible');
        }
      });
    };
  
    window.addEventListener('scroll', fadeInOnScroll);
  
    // Hover Effects on Team and Values
    const teamMembers = document.querySelectorAll('.team-member');
    const values = document.querySelectorAll('.value');
  
    teamMembers.forEach(member => {
      member.addEventListener('mouseenter', () => {
        member.style.transform = 'scale(1.05)';
        member.style.transition = 'transform 0.3s ease';
      });
  
      member.addEventListener('mouseleave', () => {
        member.style.transform = 'scale(1)';
      });
    });
  
    values.forEach(value => {
      value.addEventListener('mouseenter', () => {
        value.style.transform = 'scale(1.05)';
        value.style.transition = 'transform 0.3s ease';
      });
  
      value.addEventListener('mouseleave', () => {
        value.style.transform = 'scale(1)';
      });
    });

    const checkbox = document.querySelector('#night-mode');
    const body = document.body;
  
    // Load saved theme from localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
      body.classList.add('night');
      checkbox.checked = true;
    }
  
    // Toggle theme
    checkbox.addEventListener('change', () => {
      if (checkbox.checked) {
        body.classList.add('night');
        localStorage.setItem('theme', 'dark');
      } else {
        body.classList.remove('night');
        localStorage.setItem('theme', 'light');
      }
    });
  });
  