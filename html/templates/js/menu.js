document.addEventListener("DOMContentLoaded", function () {

    // Agregar funcionalidad para desplegar/ocultar submenús
    const submenuItems = document.querySelectorAll('.has-submenu');
    submenuItems.forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('active');
        const subMenu = item.querySelector('.sub-menu');
        //subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
    });
    });

    // Agregar funcionalidad para desplegar el menú en dispositivos m??es
    const mobileMenu = document.querySelector('.mobile-menu');
    mobileMenu.addEventListener('click', () => {
        //mobileMenu.classList.toggle('active');
        options = document.querySelector('.mobile-menu-list');
        options.classList.toggle('active');
        sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('hidden');
    });
});