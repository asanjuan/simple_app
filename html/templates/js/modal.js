function reloadPage(){
	window.location.reload();
}


function confirmarYRedirigir(msg,url) {
  // Mostrar cuadro de diálogo de confirmación
  var confirmacion = confirm(msg);

  // Comprobar el resultado de la confirmación
  if (confirmacion) {
	// El usuario hizo clic en "Aceptar", redirigir a la URL especificada
	window.location.href = url;
  } else {
	// El usuario hizo clic en "Cancelar", no hacer ninguna redirección
	//console.log("Redirección cancelada");
  }
}

function confirmarEnvio(event) {
	
	// Obtener el botón de envío que desencadenó el evento
	var boton = event.submitter;

	// Mostrar un cuadro de diálogo de confirmación con el valor del botón
	var respuesta = confirm("¿Estás seguro de que deseas enviar el formulario con el botón: " + boton.value + "?");
	
	// Devolver true (enviar formulario) o false (cancelar envío) según la respuesta del usuario
	return respuesta;
}

function mostrarModal(titulo) {
	document.getElementById("modal-title").textContent = titulo;
	document.getElementById("modal").style.display = "block";
}

function ocultarModal() {
	document.getElementById("modal").style.display = "none";
}



function mostrarFormulario(formulario) {

	document.getElementById('modal_form').style.display = "block";
	content = document.getElementById('modal-form-content');
	myform = document.getElementById('modal-form-data');
	var btn = document.getElementById('acceptButton'); 
	btn.disabled = false; //para no mandar dos veces el formulario
	
	myform.action = formulario;
	hacerLlamadaHTTP(formulario, function(response) {
		content.innerHTML = response;
		console.log(response); // Aquí puedes trabajar con la respuesta
	}); 
	
}


function ejecutarFormulario() {
	var btn = document.getElementById('acceptButton'); 
	btn.disabled = true;
	var myform = document.getElementById('modal-form-data'); 
	var formData = new FormData(myform);

	fetch( myform.action, {
		method: 'POST',
		body: formData
	})
	.then(function(response) {
		if (!response.ok) {
			throw new Error('Error al enviar el formulario');
		}
		return response.text();
	})
	.then(function(data) {
		// Maneja la respuesta del servidor
		console.log(data);
		ocultarFormulario();
		reloadPage();
	})
	.catch(function(error) {
		console.error('Error en la solicitud:', error);
	});
}


function ocultarFormulario() {
	
	document.getElementById('modal_form').style.display = "none";
	content = document.getElementById('modal-form-content');
	content.innerHTML = 'Cargando...';
}


// Simula una llamada HTTP
function ejecutarLlamada(url) {
	mostrarModal("Cargando...");
	hacerLlamadaHTTP(url, function(response) {
		ocultarModal();
		console.log(response); // Aquí puedes trabajar con la respuesta
	}); 
}



function hacerLlamadaHTTP(url, callback) {
	fetch(url)
	.then(response => response.text())
	.then(data => {
		callback(data);
	})
	.catch(error => {
		console.error('Error:', error);
	});
}


function submitForm(id_formulario, operation, titulo){
	
	if (confirm("¿Desea " + titulo+ "?")){
		
		mostrarModal(titulo);
		
		var op = document.getElementById('operation');
		op.value = operation;
		const formulario = document.getElementById(id_formulario);
		formulario.submit();

	}
}

