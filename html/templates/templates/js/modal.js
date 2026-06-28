document.addEventListener("keydown", function(e) {

    if (e.ctrlKey && e.key.toLowerCase() === "s") {
        e.preventDefault(); // evita "Guardar página del navegador"

		var op = document.getElementById('operation');
		if (op){
			op.value = 'guardar';
			const formulario = document.getElementById('form_edit');
			if (formulario) formulario.requestSubmit();
		}
    }

});


function reloadPage(){
	window.location.reload();
}


function confirmarYRedirigir(msg,url) {
  // Mostrar cuadro de diálogo de confirmación
	MyApp.ui.confirm(msg).then(confirmacion => {
		if (confirmacion) {
			// El usuario hizo clic en "Aceptar", redirigir a la URL especificada
			window.location.href = url;
		}
	});
  
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



function openAlert(titulo, mensaje){
	var myconfirm = document.getElementById("modal_alert_confirm");
	document.getElementById("modal-alert-title").innerHTML = titulo;
	document.getElementById("modal-alert-text").innerHTML = mensaje;
	document.getElementById("btn-confirm-cancel").style.display = "none";
	myconfirm.style.display = "flex";
}
function openConfirm(titulo, mensaje){
	debugger;
	var myconfirm = document.getElementById("modal_alert_confirm");
	document.getElementById("modal-alert-title").innerHTML = titulo;
	document.getElementById("modal-alert-text").innerHTML = mensaje;
	document.getElementById("btn-confirm-cancel").style.display = "inline-block";
	myconfirm.style.display = "flex";
  
}
function closeConfirm(){

	var myconfirm = document.getElementById("modal_alert_confirm");
	document.getElementById("modal-alert-title").innerHTML = '';
	document.getElementById("modal-alert-text").innerHTML = '';

	myconfirm.style.display = "none";
  
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


async function submitForm(id_formulario, operation, titulo){
	
	if (await MyApp.ui.confirm("¿Desea " + titulo+ "?")){
		
		mostrarModal(titulo);
		
		var op = document.getElementById('operation');
		op.value = operation;
		const formulario = document.getElementById(id_formulario);
		formulario.submit();

	}
}


/*
nuevo formulario modal
*/
/**
 * Crea y muestra una ventana modal con iframe apilable.
 * @param {string} url - Ruta del recurso HTML a mostrar.
 * @param {string} titulo - Título de la ventana modal.
 * @param {number} pct_alto - Porcentaje de alto del modal.
 * @param {number} pct_ancho - Porcentaje de ancho del modal.
 */
function abrirModal(url, titulo = 'Ventana', pct_alto = 80, pct_ancho = 80) {
	let modalZIndex = 1000;
	const overlay = document.createElement('div');
	overlay.className = 'modal-overlay';
	overlay.style.zIndex = modalZIndex++;

	const modal = document.createElement('div');
	modal.className = 'modal-content-new';
	modal.style.height = pct_alto + '%';
	modal.style.width = pct_ancho + '%';

	const header = document.createElement('div');
	header.className = 'modal-header';
	header.innerHTML = `<span>${titulo}</span>`;

	const closeBtn = document.createElement('span');
	closeBtn.className = 'modal-close';
	closeBtn.innerHTML = '&times;';
	closeBtn.onclick = () => cerrarModal(overlay);

	header.appendChild(closeBtn);

	const iframe = document.createElement('iframe');
	iframe.className = 'modal-iframe';
	iframe.src = url;

	modal.appendChild(header);
	modal.appendChild(iframe);
	overlay.appendChild(modal);
	document.body.appendChild(overlay);

	// Mostrar con animación
	setTimeout(() => overlay.classList.add('visible'), 10);

	// Cerrar al hacer clic fuera (opcional)
/*	overlay.onclick = (e) => {
		if (e.target === overlay) cerrarModal(overlay);
	};
*/
}

/**
 * Cierra y elimina un modal con efecto.
 * @param {HTMLElement} overlay - Elemento overlay a cerrar.
 */
function cerrarModal(overlay) {
  overlay.classList.remove('visible');
  setTimeout(() => overlay.remove(), 200);
}