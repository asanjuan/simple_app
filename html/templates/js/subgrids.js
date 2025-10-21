


//al cargar la página, cargamos todos los grids
document.addEventListener("DOMContentLoaded", function () {
	// Cargar datos del subgrid cuando la página esté cargada
	cargarSubgrids();

	//prevenir el submit del formulario con los intro
	preventFormIntro();

	//tratamiento de controles de tipo imagen
	eventosLimpiarImagen();

});

function preventFormIntro(){

	var form = document.getElementById("form_edit");
	var inputs = form.querySelectorAll("input");
	inputs.forEach(function(campotexto) {
		
		campotexto.addEventListener("keydown", function (event) {
			if (event.key === "Enter") {
				event.preventDefault(); // Evita que el formulario se envíe
			}
		});
	});

}

function eventosLimpiarImagen(){
	var btns = document.querySelectorAll("button.image-clear");
	btns.forEach(function (btn) {

		btn.addEventListener("click", function (event) {
			
			MyApp.form.clearImage( btn.getAttribute("data-column"));
			
		});

	});
}

function cargarSubgrids() {

	// Agregar event listeners para los enlaces de paginación (si los tienes)
	var gridlist = document.querySelectorAll("div.datagrid");
	gridlist.forEach(function (grid) {

		cargarSubgrid(grid);

	});

}

function cambiarVista(select) {
	//alert("Nuevo valor seleccionado:" + select.value);
	var grid = document.getElementById("maingrid");
	grid.setAttribute("data-view-id", select.value);
	cargarSubgrid(grid);
}


function cargarSubgrid(grid) {

	cargarPaginaSubgrid(grid, 1);
}

function sendEvent(grid, event_name) {

	// Crear el evento personalizado
	const evento = new CustomEvent(event_name, {
		detail: {
			object: grid
		}
	}
	);

	// Lanzar el evento
	grid.dispatchEvent(evento);
}

function agregarListenersPaginacion(grid) {

	// Agregar event listeners para los enlaces de paginación (si los tienes)
	var linksPaginacion = grid.querySelectorAll("a.grid-pagina");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			var page = this.getAttribute("data-page");
			cargarPaginaSubgrid(grid, page);
		});
	});


	// Agregar event listeners para los enlaces de borrado de elementos (si existen)
	var linksPaginacion = grid.querySelectorAll("a.btn-del");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			borrarRegistros(grid);
		});
	});

	// Agregar event listeners para los enlaces de DUPLICADO de elementos (si existen)
	var linksPaginacion = grid.querySelectorAll("a.btn-duplicate");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			duplicarRegistros(grid);
		});
	});

	// Agregar event listeners para los enlaces de DUPLICADO de elementos (si existen)
	var linksPaginacion = grid.querySelectorAll("a.btn-up");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			UpRegistros(grid);
		});
	});

	// Agregar event listeners para los enlaces de DUPLICADO de elementos (si existen)
	var linksPaginacion = grid.querySelectorAll("a.btn-custom");
	linksPaginacion.forEach(function (link) {
		var operation = link.getAttribute("data-operation");
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			sendCustomOperation(grid, operation);
		});
	});

	// Agregar event listeners para los enlaces de DUPLICADO de elementos (si existen)
	var linksPaginacion = grid.querySelectorAll("a.btn-down");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			DownRegistros(grid);
		});
	});

	//agregar evento para texto de búsqueda

	var linksPaginacion = grid.querySelectorAll("a.btn-search");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			//una búsqueda por defecto saca la primera página
			cargarPaginaSubgrid(grid, 1);
		});
	});

	var linksPaginacion = grid.querySelectorAll("a.btn-reset");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			//una búsqueda por defecto saca la primera página
			search_text = grid.querySelector("input[type='text']");
			search_text.value = "";
			cargarPaginaSubgrid(grid, 1);
		});
	});


	// Agregar event listeners para botón de inserción rápida
	var linksPaginacion = grid.querySelectorAll("a.btn-new");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			renderizarQuickCreate(grid);
		});
	});

	// Agregar event listeners para botón de exportación
	var linksPaginacion = grid.querySelectorAll("a.btn-export");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			exportar_csv(grid);
		});
	});

	//selector de vistas
	var select = grid.querySelectorAll("select");
	select.forEach(function (item) {
		item.addEventListener("change", function (event) {
			grid.setAttribute("data-view-id", item.value);
			cargarSubgrid(grid);
		});
	});

	var campotexto = grid.querySelector("input[type=text]");
	/*
	campotexto.addEventListener("change", function(event) {
		event.preventDefault(); // Evitar que el enlace cambie la página
		//una búsqueda por defecto saca la primera página
		cargarPaginaSubgrid(grid,1);
	  });
	*/
	campotexto.addEventListener("keydown", function (event) {
		if (event.key === "Enter") {
			event.preventDefault(); // Evita que el formulario se envíe
			cargarSubgrid(grid);
		}
	});

	// Agregar event listeners para botón de exportación
	var tabla = grid.querySelector("table");
	if (tabla) {
		addSortingTable(tabla.id);
	}

}



function cargarPaginaSubgrid(grid, page) {
	////debugger;
	//grid = document.getElementById("subgridContainer");
	url = grid.getAttribute("data-gridurl");
	datafield = grid.getAttribute("data-field");
	datavalue = grid.getAttribute("data-value");
	pagesize = grid.getAttribute("data-page-size");
	view = grid.getAttribute("data-view-id");
	enabled = grid.getAttribute("data-enabled");
	search_text = grid.querySelector("input[type='text']")?.value;
	operation = "select";

	//asignamos la pagina al grid para futuros eventos
	grid.setAttribute("data-page", page);

	// Configurar los datos del filtro
	var filtro = {
		operation: operation,
		// Define tu filtro aquí, por ejemplo:
		field: datafield,
		value: datavalue,
		pagesize: pagesize,
		page: page,
		view: view,
		search_text: search_text,
		enabled: enabled
	};

	// Realizar una solicitud fetch para obtener los datos del subgrid
	fetch(url, {
		method: "POST",
		//headers: {
		//  "Content-Type": "application/json"
		//},
		body: JSON.stringify(filtro)
		//body: filtro
	})
		.then(response => response.text())
		.then(data => {

			// Insertar los datos del subgrid en el contenedor
			grid.innerHTML = data;
			//agregar ordenación

			// Agregar event listeners para la paginación (opcional)
			agregarListenersPaginacion(grid);
			sendEvent(grid, "OnRefresh");
		})
		.catch(error => console.error('Error al cargar el subgrid:', error));
}


function borrarRegistros(grid) {

	let data_array = [];



	// Obtener todas las casillas de verificación de las filas
	var checkboxes = grid.querySelectorAll('input[type=checkbox]');
	// Iterar sobre todas las casillas de verificación y establecer su estado a igual que el checkbox de encabezado
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) data_array.push(checkboxes[i].value);
	}

	if (data_array.length > 0) {

		if (confirm("¿Desea eliminar los registros seleccionados?")) {

			mostrarModal("Eliminando...");
			url = grid.getAttribute("data-gridurl");
			datafield = grid.getAttribute("data-field");
			datavalue = grid.getAttribute("data-value");
			pagesize = grid.getAttribute("data-page-size");
			view = grid.getAttribute("data-view-id");
			page = grid.getAttribute("data-page");
			enabled = grid.getAttribute("data-enabled");
			search_text = grid.querySelector("input[type='text']")?.value;


			// Configurar los datos del filtro
			var op_data = {
				operation: "delete",
				enabled: enabled,
				// Define tu filtro aquí, por ejemplo:
				field: datafield,
				value: datavalue,
				pagesize: pagesize,
				page: page,
				view: view,
				list: data_array,
				search_text: search_text
			};

			// Realizar una solicitud fetch para obtener los datos del subgrid
			fetch(url, {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify(op_data)
			})
				.then(response => response.text())
				.then(data => {

					// Insertar los datos del subgrid en el contenedor
					grid.innerHTML = data;
					// Agregar event listeners para la paginación (opcional)
					agregarListenersPaginacion(grid);
					ocultarModal();
					sendEvent(grid, "OnDelete");
				})
				.catch(error => {
					console.error('Error al cargar el subgrid:', error);
					ocultarModal();
				});
		}
	}
}


function sendCustomOperation(grid, operation) {
	let data_array = [];

	//debugger;

	// Obtener todas las casillas de verificación de las filas
	var checkboxes = grid.querySelectorAll('td input[type=checkbox]');
	// Iterar sobre todas las casillas de verificación y establecer su estado a igual que el checkbox de encabezado
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) data_array.push(checkboxes[i].value);
	}

	if (data_array.length > 0) {

		if (confirm("¿Desea aplicar la operación a los registros seleccionados?")) {

			mostrarModal("Ejecutando...");
			url = grid.getAttribute("data-gridurl");
			datafield = grid.getAttribute("data-field");
			datavalue = grid.getAttribute("data-value");
			pagesize = grid.getAttribute("data-page-size");
			view = grid.getAttribute("data-view-id");
			page = grid.getAttribute("data-page");
			enabled = grid.getAttribute("data-enabled");
			search_text = grid.querySelector("input[type='text']")?.value;


			// Configurar los datos del filtro
			var op_data = {
				operation: operation,
				enabled: enabled,
				// Define tu filtro aquí, por ejemplo:
				field: datafield,
				value: datavalue,
				pagesize: pagesize,
				page: page,
				view: view,
				list: data_array,
				search_text: search_text
			};

			// Realizar una solicitud fetch para obtener los datos del subgrid
			fetch(url, {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify(op_data)
			})
				.then(response => response.text())
				.then(data => {

					// Insertar los datos del subgrid en el contenedor
					grid.innerHTML = data;
					// Agregar event listeners para la paginación (opcional)
					agregarListenersPaginacion(grid);
					ocultarModal();
					sendEvent(grid, "OnCustom_" + operation);
				})
				.catch(error => {
					console.error('Error al cargar el subgrid:', error);
					ocultarModal();
				});
		}
	}

}


function duplicarRegistros(grid) {

	let data_array = [];



	// Obtener todas las casillas de verificación de las filas
	var checkboxes = grid.querySelectorAll('td input[type=checkbox]');
	// Iterar sobre todas las casillas de verificación y establecer su estado a igual que el checkbox de encabezado
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) data_array.push(checkboxes[i].value);
	}

	if (data_array.length > 0) {

		if (confirm("¿Desea duplicar los registros seleccionados?")) {

			mostrarModal("Duplicando...");
			url = grid.getAttribute("data-gridurl");
			datafield = grid.getAttribute("data-field");
			datavalue = grid.getAttribute("data-value");
			pagesize = grid.getAttribute("data-page-size");
			view = grid.getAttribute("data-view-id");
			page = grid.getAttribute("data-page");
			enabled = grid.getAttribute("data-enabled");
			search_text = grid.querySelector("input[type='text']")?.value;


			// Configurar los datos del filtro
			var op_data = {
				operation: "duplicate",
				enabled: enabled,
				// Define tu filtro aquí, por ejemplo:
				field: datafield,
				value: datavalue,
				pagesize: pagesize,
				page: page,
				view: view,
				list: data_array,
				search_text: search_text
			};

			// Realizar una solicitud fetch para obtener los datos del subgrid
			fetch(url, {
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify(op_data)
			})
				.then(response => response.text())
				.then(data => {

					// Insertar los datos del subgrid en el contenedor
					grid.innerHTML = data;
					// Agregar event listeners para la paginación (opcional)
					agregarListenersPaginacion(grid);
					ocultarModal();
					sendEvent(grid, "OnDuplicate");
				})
				.catch(error => {
					console.error('Error al cargar el subgrid:', error);
					ocultarModal();
				});
		}
	}
}


//UP AND DOWN 

function UpRegistros(grid) {

	let data_array = [];



	// Obtener todas las casillas de verificación de las filas
	var checkboxes = grid.querySelectorAll('input[type=checkbox]');
	// Iterar sobre todas las casillas de verificación y establecer su estado a igual que el checkbox de encabezado
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) data_array.push(checkboxes[i].value);
	}

	if (data_array.length > 0) {

		//if (confirm("¿Desea eliminar los registros seleccionados?")){

		//mostrarModal("Eliminando...");
		url = grid.getAttribute("data-gridurl");
		datafield = grid.getAttribute("data-field");
		datavalue = grid.getAttribute("data-value");
		pagesize = grid.getAttribute("data-page-size");
		view = grid.getAttribute("data-view-id");
		page = grid.getAttribute("data-page");
		enabled = grid.getAttribute("data-enabled");
		search_text = grid.querySelector("input[type='text']")?.value;


		// Configurar los datos del filtro
		var op_data = {
			operation: "up",
			enabled: enabled,
			// Define tu filtro aquí, por ejemplo:
			field: datafield,
			value: datavalue,
			pagesize: pagesize,
			page: page,
			view: view,
			list: data_array,
			search_text: search_text
		};

		// Realizar una solicitud fetch para obtener los datos del subgrid
		fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify(op_data)
		})
			.then(response => response.text())
			.then(data => {

				// Insertar los datos del subgrid en el contenedor
				grid.innerHTML = data;
				// Agregar event listeners para la paginación (opcional)
				agregarListenersPaginacion(grid);
				ocultarModal();
				sendEvent(grid, "OnUp");
			})
			.catch(error => {
				console.error('Error al cargar el subgrid:', error);
				//ocultarModal();
			});
		//}
	}
}


function DownRegistros(grid) {

	let data_array = [];



	// Obtener todas las casillas de verificación de las filas
	var checkboxes = grid.querySelectorAll('input[type=checkbox]');
	// Iterar sobre todas las casillas de verificación y establecer su estado a igual que el checkbox de encabezado
	for (var i = 0; i < checkboxes.length; i++) {
		if (checkboxes[i].checked) data_array.push(checkboxes[i].value);
	}

	if (data_array.length > 0) {

		//if (confirm("¿Desea eliminar los registros seleccionados?")){

		//mostrarModal("Eliminando...");
		url = grid.getAttribute("data-gridurl");
		datafield = grid.getAttribute("data-field");
		datavalue = grid.getAttribute("data-value");
		pagesize = grid.getAttribute("data-page-size");
		view = grid.getAttribute("data-view-id");
		page = grid.getAttribute("data-page");
		enabled = grid.getAttribute("data-enabled");
		search_text = grid.querySelector("input[type='text']")?.value;


		// Configurar los datos del filtro
		var op_data = {
			operation: "down",
			enabled: enabled,
			// Define tu filtro aquí, por ejemplo:
			field: datafield,
			value: datavalue,
			pagesize: pagesize,
			page: page,
			view: view,
			list: data_array,
			search_text: search_text
		};

		// Realizar una solicitud fetch para obtener los datos del subgrid
		fetch(url, {
			method: "POST",
			headers: {
				"Content-Type": "application/json"
			},
			body: JSON.stringify(op_data)
		})
			.then(response => response.text())
			.then(data => {

				// Insertar los datos del subgrid en el contenedor
				grid.innerHTML = data;
				// Agregar event listeners para la paginación (opcional)
				agregarListenersPaginacion(grid);
				ocultarModal();
				sendEvent(grid, "OnDown");
			})
			.catch(error => {
				console.error('Error al cargar el subgrid:', error);
				//ocultarModal();
			});
		//}
	}
}



function renderizarQuickCreate(grid) {

	url = grid.getAttribute("data-gridurl");
	datafield = grid.getAttribute("data-field");
	datavalue = grid.getAttribute("data-value");
	pagesize = grid.getAttribute("data-page-size");
	view = grid.getAttribute("data-view-id");
	page = grid.getAttribute("data-page");
	search_text = grid.querySelector("input[type='text']")?.value;
	enabled = grid.getAttribute("data-enabled");

	// Configurar los datos del filtro
	var op_data = {
		operation: "render",
		enabled: enabled,
		// Define tu filtro aquí, por ejemplo:
		field: datafield,
		value: datavalue,
		pagesize: pagesize,
		page: page,
		view: view,
		search_text: search_text
	};

	// Realizar una solicitud fetch para obtener los datos del subgrid
	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify(op_data)
	})
		.then(response => response.text())
		.then(data => {

			// Insertar los datos del subgrid en el contenedor
			//grid.innerHTML = data;
			mostrarQuickCreate(grid, data);
			// Agregar event listeners para la paginación (opcional)
			//agregarListenersPaginacion(grid);

		})
		.catch(error => {
			console.error('Error al cargar el subgrid:', error);

		});

}



function mostrarQuickCreate(grid, data) {

	document.getElementById('modal_form').style.display = "block";
	content = document.getElementById('modal-form-content');
	content.innerHTML = data;

	var btn = document.getElementById('acceptButton');

	btn.disabled = false; //para no mandar dos veces el formulario
	btn.addEventListener("click", function (event) {

		ejecutarQuickCreate(grid);
	});

}


function ejecutarQuickCreate(grid) {
	//debugger;
	var btn = document.getElementById('acceptButton');
	btn.disabled = true;
	var myform = document.getElementById('modal-form-data');


	if (!validarFormulario(myform)) return;
	var formData = new FormData(myform);

	url = grid.getAttribute("data-gridurl");
	datafield = grid.getAttribute("data-field");
	datavalue = grid.getAttribute("data-value");
	pagesize = grid.getAttribute("data-page-size");
	view = grid.getAttribute("data-view-id");
	page = grid.getAttribute("data-page");
	enabled = grid.getAttribute("data-enabled");
	search_text = grid.querySelector("input[type='text']")?.value;

	// Configurar los datos del filtro
	var op_data = {
		operation: "insert",
		enabled: enabled,
		// Define tu filtro aquí, por ejemplo:
		field: datafield,
		value: datavalue,
		pagesize: pagesize,
		page: page,
		view: view,
		search_text: search_text
		//,form_data : formData
	};
	formData.append("op_data", JSON.stringify(op_data));


	// Realizar una solicitud fetch para obtener los datos del subgrid
	fetch(url, {
		method: "POST",

		body: formData
	})
		.then(response => response.text())
		.then(data => {
			console.log(data);
			cargarPaginaSubgrid(grid, 1);
			ocultarFormulario();
			sendEvent(grid, "AfterInsert");
		})
		.catch(error => {
			console.error('Error al cargar el subgrid:', error);
			ocultarFormulario();

		});

}

function validarFormulario(myform) {
	let esValido = true; // Variable para rastrear si el formulario es válido

	// Seleccionar todos los campos requeridos
	const camposRequeridos = myform.querySelectorAll('[required]');

	// Limpiar errores anteriores
	camposRequeridos.forEach(campo => {
		campo.classList.remove('campo-error'); // Eliminar la clase de error, si existe
	});

	// Verificar si los campos requeridos tienen un valor
	camposRequeridos.forEach(campo => {
		if (!campo.value.trim()) {
			esValido = false; // Marcar como inválido
			campo.classList.add('campo-error'); // Añadir clase para resaltar el campo
		}
	});

	return esValido; // Devuelve true si todos los campos requeridos están completos
}

function exportar_csv(grid) {
	//debugger;
	var btn = document.getElementById('acceptButton');
	//btn.disabled = true;
	var myform = document.getElementById('modal-form-data');
	var formData = new FormData(myform);

	var formDataObject = {};
	formData.forEach((value, key) => {
		formDataObject[key] = value;
	});

	url = grid.getAttribute("data-gridurl");
	datafield = grid.getAttribute("data-field");
	datavalue = grid.getAttribute("data-value");
	pagesize = grid.getAttribute("data-page-size");
	view = grid.getAttribute("data-view-id");
	page = grid.getAttribute("data-page");
	enabled = grid.getAttribute("data-enabled");
	search_text = grid.querySelector("input[type='text']")?.value;

	// Configurar los datos del filtro
	var op_data = {
		operation: "export",
		// Define tu filtro aquí, por ejemplo:
		field: datafield,
		value: datavalue,
		pagesize: pagesize,
		page: page,
		view: view,
		search_text: search_text,
		form_data: formDataObject
	};

	// Realizar una solicitud fetch para obtener los datos del subgrid
	fetch(url, {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify(op_data)
	})
		.then(response => {
			if (!response.ok) {
				throw new Error('Error al obtener el archivo CSV');
			}

			// Obtener el nombre del archivo del encabezado Content-Disposition
			const contentDisposition = response.headers.get('Content-Disposition');
			let filename = 'archivo.csv'; // Nombre predeterminado si no se encuentra en los headers

			if (contentDisposition && contentDisposition.includes('filename=')) {
				const filenameMatch = contentDisposition.match(/filename="?([^"]+)"?/);
				if (filenameMatch && filenameMatch[1]) {
					filename = filenameMatch[1]; // Usar el nombre del archivo especificado por el servidor
				}
			}

			return response.blob().then(blob => ({ blob, filename }));
		})
		.then(({ blob, filename }) => {
			const url = URL.createObjectURL(blob);
			const a = document.createElement('a');
			a.href = url;
			a.download = filename;
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
			URL.revokeObjectURL(url);
		})
		.catch(error => {
			console.error('Error al cargar el subgrid:', error);
			document.getElementById('modal_form').style.display = "none";

		});

}
