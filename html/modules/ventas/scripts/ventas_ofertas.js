document.addEventListener("DOMContentLoaded", onload);


function onload(){
    
    //suscribimos eventos
    var ean = document.getElementById('grid_lineas');
    ean.addEventListener('AfterInsert',refrescarTotales);
    ean.addEventListener('OnDelete',refrescarTotales);
    
}

function refrescarTotales(e){
    
    let id = MyApp.form.getFieldValue("id");
    
    MyApp.api.retrieve("ventas_ofertas",id).then(array => {
        let r = array[0]; 
        
        MyApp.form.setFieldValue("importe_total", parseFloat(r.importe_total).toFixed(2) );
        MyApp.form.setFieldValue("total_neto", parseFloat(r.total_neto).toFixed(2) );
        MyApp.form.setFieldValue("total_impuestos", parseFloat(r.total_impuestos).toFixed(2) );

    });
    
}