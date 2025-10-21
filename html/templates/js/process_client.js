
//al cargar la página, cargamos todos los grids
document.addEventListener("DOMContentLoaded", function () {
	// Cargar datos del subgrid cuando la página esté cargada
	cargarProcesos();

});

function cargarProcesos() {

	// Agregar event listeners para los enlaces de paginación (si los tienes)
	var procs = document.querySelectorAll("div.entity_process");
	procs.forEach(function (proc) {

		cargarProceso(proc);

	});

}

function cargarProceso(proc) {

    
    var processId = proc.getAttribute("data-process-id");
	var url = proc.getAttribute("data-url");
    var dataId = proc.getAttribute("data-id");
	var enabled = proc.getAttribute("data-enabled");

	var operation = "select";

	// Configurar los datos del filtro
	var filtro = {
		operation: operation,
		// Define tu filtro aquí, por ejemplo:
        process_id: processId,
        data_id: dataId,
		enabled: enabled
	};
	
	// Realizar una solicitud fetch para obtener los datos del subgrid
	fetch(url, {
		method: "POST",
		body: JSON.stringify(filtro)
	})
		.then(response => response.json())
		.then(data => {
            //debugger;
			console.log(data);
            // Limpiar el contenido actual
            proc.innerHTML = "";
            var processlist  = document.createElement("div");
            processlist.classList.add("process_list");
            processlist.classList.add("flex-rows");
            
            proc.appendChild(processlist);

            data.fases.forEach(function(fase){
                if (fase.tipo_fase != 1 || fase.activo || fase.completado){
					var faseDiv = document.createElement("div");
					faseDiv.classList.add("process_step");
					if (fase.tipo_fase == 1){
						faseDiv.classList.add("completed");
					}else  if (fase.activo) {
						faseDiv.classList.add("active");
					}else if (fase.completado) {
						faseDiv.classList.add("completed");
					}
					faseDiv.innerHTML = "<strong>"  + fase.nombre + "</strong> ";
					processlist.appendChild(faseDiv);
				}
				
            });


			var buttons  = document.createElement("div");
            buttons.classList.add("process_button_list");
            //buttons.classList.add("flex-rows");
            data.transiciones.forEach(function(trans){
                var btn = document.createElement("button");
				
                btn.classList.add("process_button");
				btn.addEventListener("click", function (event) {
					event.preventDefault(); // Evitar que el enlace cambie la página
					
					transicion(proc, trans.id);
				});
                btn.innerHTML = trans.nombre;
                buttons.appendChild(btn);
            });
            proc.appendChild(buttons);


		})
		.catch(error => console.error('Error al cargar el proceso:', error));
}

function transicion(proc, id_transicion) {
	
	console.log("Transición solicitada: " + id_transicion);

    var processId = proc.getAttribute("data-process-id");
	var url = proc.getAttribute("data-url");
    var dataId = proc.getAttribute("data-id");
	var enabled = proc.getAttribute("data-enabled");

	var operation = "transition";

	// Configurar los datos del filtro
	var filtro = {
		operation: operation,
		// Define tu filtro aquí, por ejemplo:
        process_id: processId,
        data_id: dataId,
		transition_id: id_transicion,
		enabled: enabled
	};
	
	// Realizar una solicitud fetch para obtener los datos del subgrid
	fetch(url, {
		method: "POST",
		body: JSON.stringify(filtro)
	})
		.then(response => response.text())
		.then(data => {
            //debugger;
			console.log(data);

			// Recargar el proceso para reflejar los cambios
			reloadPage();

		})
		.catch(error => console.error('Error al cargar el proceso:', error));
}

