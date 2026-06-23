document.addEventListener("DOMContentLoaded", onload);


function onload(){
    
    const boton = document.querySelector('button[name="btn_modal"]');

    // Ejemplo de uso: añadir un evento
    boton.addEventListener('click', on_btnclick );
}

function on_btnclick(){
    abrirModal('./modules/ventas/code/codigo_barras.html','Lector de QR');
}