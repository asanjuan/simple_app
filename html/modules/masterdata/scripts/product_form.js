document.addEventListener("DOMContentLoaded", onload);
document.addEventListener("BARCODE_READ", onBarcodeRead);

function onload(){
    
    const boton = document.querySelector('button[name="barcode"]');

    // Ejemplo de uso: añadir un evento
    boton.addEventListener('click', on_btnclick );
}

function on_btnclick(){
    abrirModal('./modules/core/code/codigo_barras.html','Lector de QR', 95,95);
}

function onBarcodeRead(e){
    
    //console.log(e);
    MyApp.form.setFieldValue('ean_code', e.detail.readed);
    MyApp.ui.alert("Código EAN actualizado:<br/>" + e.detail.readed);
    
}