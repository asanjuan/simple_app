document.addEventListener("DOMContentLoaded", function () {

	const sidebar1 = document.querySelector('.sidebar');
    const sidebarHidden1 = sessionStorage.getItem('sidebarHidden');

    if (sidebarHidden1 === '1') {
        sidebar1.classList.add('hidden'); //lo muestra oculto
    }else {
		sidebar1.classList.remove('hidden'); //muestra el sidebar. Por defecto, desde servidor se envía oculto.
	}
	
    

    // Agregar funcionalidad para desplegar/ocultar submenús
    const submenuItems = document.querySelectorAll('.has-submenu');
    submenuItems.forEach(item => {
        
        var menuitems = item.querySelectorAll('a.menuitem');
        menuitems.forEach( function (link)  {
            link.addEventListener('click', (linkevent) => {
                linkevent.stopPropagation();
            });
        });

        item.addEventListener('click', (evt) => {
            
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
		// Guardar estado
		const isHidden = sidebar.classList.contains('hidden');
		sessionStorage.setItem('sidebarHidden', isHidden ? '1' : '0');
    });
});