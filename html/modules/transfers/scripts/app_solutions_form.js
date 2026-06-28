document.addEventListener("DOMContentLoaded", onload);


function onload(){
    
    const boton = document.querySelector('button[name="addobject"]');

    // Ejemplo de uso: añadir un evento
    boton.addEventListener('click', on_btnclick );
}

function on_btnclick(){
    abrirModal('./modules/transfers/code/IMPORTADOR_OBJETOS_TRANSPORTE.html','Importar Objetos');
}