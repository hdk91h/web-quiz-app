const navbarBtn = document.querySelector('.mobile-menu-btn');
const navbar = document.querySelector('.top-nav');


navbarBtn.addEventListener('click', function() {
    if (navbar.style.display === 'block') {
        navbar.style.display = 'none';
    } else {
        navbar.style.display = 'block';
    }
});