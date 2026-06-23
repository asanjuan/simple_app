document.addEventListener("DOMContentLoaded", onload);


function onload(){
    
    const boton = document.querySelector('button[name="copiar"]');

    // Ejemplo de uso: añadir un evento
    boton.addEventListener('click', on_btnclick );
}

function on_btnclick(){
    
    let id = MyApp.form.getFieldValue("id");
    
    MyApp.api.retrieve("keypass",id).then(array => {
        let r = array[0]; 
        
        MyApp.api.decypher(r['pwd']).then(response => {
            if (navigator.clipboard){
                navigator.clipboard.writeText(response['message']);
            }else{
                old_copy(response['message']);
            }
            MyApp.ui.alert('Texto copiado','Simple App');
        })

    });
    
}

function old_copy(msg){
    const textArea = document.createElement('textarea');
    textArea.value = msg;
    textArea.style.opacity = 0;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
      const success = document.execCommand('copy');
      console.log(`Text copy was ${success ? 'successful' : 'unsuccessful'}.`);
    } catch (err) {
      console.error(err.name, err.message);
    }
    document.body.removeChild(textArea);
  }