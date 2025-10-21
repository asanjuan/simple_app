function  addSortingTable(id_tabla){
	
	// Obtén la tabla y las filas de datos
	var tabla = document.getElementById(id_tabla);
	var filas = tabla.querySelectorAll("tbody tr");

	// Agrega un evento click a cada cabecera
	var cabeceras = tabla.querySelectorAll("thead th");
	
	cabeceras.forEach(function(cabecera, indice) {
		cabecera.sort_mode = 'asc';
		
		cabecera.addEventListener("click", function() {
			
			ordenarTabla(cabecera,indice,tabla,filas);
		});
		
	});
}


// Función para ordenar la tabla por la columna indicada
function ordenarTabla(cabecera, columna,tabla, filas) {
    var datos = Array.from(filas);
	//console.log("hola");
    datos.sort(function(a, b) {
		//console.log(a.cells[columna].textContent + '  = ' + a.cells[columna].textContent);
		var valorA = parseFloat(a.cells[columna].textContent);
		var valorB = parseFloat(b.cells[columna].textContent);
		
        var textoA = a.cells[columna].textContent;
        var textoB = b.cells[columna].textContent;
		
		var resultado = 0;
		
		if (!isNaN(valorA) && !isNaN(valorB)) {
			resultado = valorA - valorB;
			//console.log('numeros');
		} else if (!isNaN(valorA)) {
			resultado = -1; // Valores numéricos primero
			//console.log('numero vs texto');
		} else if (!isNaN(valorB)) {
			resultado = 1; // Valores numéricos primero
			//console.log('texto vs numero');
		} else {
			resultado = textoA.localeCompare(textoB); // Ordenación alfabética
			//console.log('textos');
		}	
		
        if (cabecera.sort_mode == 'desc') resultado = -resultado;
		
		//console.log(resultado);
		
		return resultado;
    });

    datos.forEach(function(fila) {
        tabla.querySelector("tbody").appendChild(fila);
    });
	if (cabecera.sort_mode == 'asc') cabecera.sort_mode = 'desc';
	else cabecera.sort_mode = 'asc';
}

function seleccionarTodasLasFilas(id_tabla, checkbox) {
	
	var tabla = document.getElementById(id_tabla);
	
	// Obtener todas las casillas de verificación de las filas
	var checkboxes = tabla.querySelectorAll('input[type=checkbox]');

	// Iterar sobre todas las casillas de verificación y establecer su estado a igual que el checkbox de encabezado
	for (var i = 0; i < checkboxes.length; i++) {
		checkboxes[i].checked = checkbox.checked;
	}
}



function redirigir(url){
	window.location.href = url;
}
