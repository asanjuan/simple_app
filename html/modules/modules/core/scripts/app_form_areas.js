document.addEventListener("DOMContentLoaded", onload);


function onload(){
    //establecemos el prefiltro de los lookup
    let id_form = MyApp.form.getFieldValue("id_form" );
    
    let obj = document.getElementById("id_tab");
    obj.setAttribute("data-field","id_form");
    obj.setAttribute("data-value",id_form);
}
