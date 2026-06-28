document.addEventListener("DOMContentLoaded", onload);


function onload(){
    //establecemos el prefiltro de los lookup
    let id_proceso = MyApp.form.getFieldValue("id_proceso" );
    console.log(id_proceso);
    
    let obj = document.getElementById("id_fase_inicio");
    obj.setAttribute("data-field","id_proceso");
    obj.setAttribute("data-value",id_proceso);
    
    obj = document.getElementById("id_fase_fin");
    obj.setAttribute("data-field","id_proceso");
    obj.setAttribute("data-value",id_proceso);
}

