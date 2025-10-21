//registrarse a eventos

window.addEventListener("load", onload);
MyApp.utils.loadScript ("modules/ventas/scripts/test.js", onloadlib);

function onload(){
    MyApp.ui.notify('en el load del javascript');
    
    const jsonQuery = `{
        "select": [
            "*"
        ],
        "from": {
            "table": "productos",
            "alias": ""
        },
        "joins": [],
        "where": "",
        "groupBy": [],
        "having": "",
        "orderBy": [],
        "limit": null,
        "distinct": true
    }`;
    
    
    MyApp.api.query((jsonQuery))
    .then(data => { 
       let to_update = []; //en upsert se manda una lista de objetos a actualizar o insertar
       
        console.log(data); 
        data.forEach(producto => {
            console.log(`Producto: ${producto.nombre}, Peso: ${producto.peso}`);
            to_update.push({ id: producto.id, peso: 50});
        });
        //ahora agregamos tb uno nuevo
        //to_update.push({nombre:"uno nuevo desde JS"});
        
        //llamamos a la api
        MyApp.api.upsert("productos",to_update).then( data => console.log(data) ).catch( error => console.log(error));
        
    } )
    .catch( error => { 
        console.error(error);
    });
    
}
function onloadlib(){
    MyApp.utils.log('Libreria cargada');

}

