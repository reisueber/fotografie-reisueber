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