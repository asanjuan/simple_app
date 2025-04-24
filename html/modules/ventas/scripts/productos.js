//registrarse a eventos

document.addEventListener("DOMContentLoaded", onload);


function onload(){
    MyApp.utils.log('en el load');
    
    //suscribimos eventos
    var ean = document.getElementById('ean_code');
    ean.addEventListener('change',onChangeEan);
    
    var btn = document.querySelectorAll('button[name="test"]')[0];
    btn.addEventListener('click',onClickBtn);
    

}

function onChangeEan(){
    MyApp.ui.notify('en el onchange');
}


function onClickBtn(){
    MyApp.form.setFieldValue("ean_code",9999); //dispara onchange también
    MyApp.form.setFieldValue("peso","abc");
}