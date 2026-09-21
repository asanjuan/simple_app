document.addEventListener("DOMContentLoaded", onload);
document.addEventListener("BARCODE", onBarcodeRead);

function onload(){
    
    const boton = document.querySelector('button[name="btn_picking"]');

    // Ejemplo de uso: añadir un evento
    boton.addEventListener('click', on_btnclick );
}

function on_btnclick(){
    abrirModal('./modules/warehouse/code/picking_documento.html','Lector de QR', 95,95);
}

function onBarcodeRead(e){
    
    //hacer la magia aquí de insertar el registro en los movimientos de almacen
    console.log(e.detail);
    debugger;
    let id_document = MyApp.form.getFieldValue("id" );
    let new_record = {
        id_documento : id_document,
        id_producto: e.detail.product["id"],
        cantidad: e.detail.quantity
        
    };
    MyApp.api.upsert("alm_movimientos", [ new_record ]).then( resp => {
        cargarSubgrids() ;        
    });
    

    
}