console.log("Navigation Plugin geladen!");

window.addEventListener('scroll', function() {
    const header = document.getElementById('main-header');
    if (window.scrollY > 40) {
        header.classList.add('scrolled');
        header.classList.remove('absolute');
    } else {
        header.classList.remove('scrolled');
        header.classList.add('absolute');
    }
});


// Mobile Menu Functionality
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuContent = document.getElementById('mobile-menu-content');
    const closeMobileMenu = document.getElementById('close-mobile-menu');

    function toggleMenu() {
        mobileMenu.classList.toggle('hidden');
        mobileMenuContent.classList.toggle('translate-x-full');
    }

    mobileMenuButton.addEventListener('click', toggleMenu);
    closeMobileMenu.addEventListener('click', toggleMenu);
    
    // Schließe Menü wenn außerhalb geklickt wird
    mobileMenu.addEventListener('click', (e) => {
        if (e.target === mobileMenu) {
            toggleMenu();
        }
    });
});