

function lookup_change(id_codigo, id_descripcion){
	//alert('llamada lookup_change');
	var objdesc = document.getElementById(id_descripcion);
	var objid = document.getElementById(id_codigo);
	if (objdesc.value == ""){
		objid.value = "";
	}else {

		openLookup( id_codigo, id_descripcion,1);
	}
}




function openLookup(id_codigo, id_descripcion, page) {
    //debugger;
    grid = document.getElementById("lookup_grid");
	field = document.getElementById(id_codigo);
  document.getElementById('modal_lookup').style.display = 'block';

    url = "forms/lookup.php";
    datafield = field.getAttribute("data-field");
    datavalue = field.getAttribute("data-value");
    view = grid.getAttribute("data-view-id");

	  filter =  grid.querySelector("input[type='text']");
    if (filter){
      search_text = filter.value;
    }else {
      search_text = document.getElementById(id_descripcion).value;
    }
    
  

	operation = "select";
    tabla = field.getAttribute("data-table");
	//asignamos la pagina al grid para futuros eventos
	grid.setAttribute("data-page",page);
	
    // Configurar los datos del filtro
    var filtro = {
	  operation: operation,
      // Define tu filtro aquí, por ejemplo:
      field: datafield,
      value: datavalue,
      page: page,
      view: view,
	  table: tabla,
	  search_text: search_text,
	  lookup_id_field: id_codigo,
	  lookup_id_desc: id_descripcion
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
		
        // Agregar event listeners para la paginación (opcional)
        agregarListenersLookup(grid,id_codigo, id_descripcion, page);
      })
      .catch(error => console.error('Error al cargar el subgrid:', error));
  }




function closeLookup(){
	//alert('llamada closeLookup');
	var grid = document.getElementById('lookup_grid');
	grid.innerHTML = '';
	document.getElementById('modal_lookup').style.display = 'none';
}

function lookupElementClick(id_codigo, id_descripcion, valor_codigo, valor_descripcion){
	//alert('llamada lookupElementClick');
	document.getElementById(id_codigo).value = valor_codigo;
	document.getElementById(id_descripcion).value  = valor_descripcion;
	closeLookup();
}



function agregarListenersLookup(grid,id_codigo, id_descripcion, page) {

  // Agregar event listeners para los enlaces de paginación (si los tienes)
  var linksPaginacion = grid.querySelectorAll("a.grid-pagina");
  linksPaginacion.forEach(function(link) {
    link.addEventListener("click", function(event) {
      event.preventDefault(); // Evitar que el enlace cambie la página
      var page = this.getAttribute("data-page");
      openLookup(id_codigo, id_descripcion, page);
    });
  });



//agregar evento para texto de búsqueda

var linksPaginacion = grid.querySelectorAll("a.btn-search");
  linksPaginacion.forEach(function(link) {
    link.addEventListener("click", function(event) {
      event.preventDefault(); // Evitar que el enlace cambie la página
  //una búsqueda por defecto saca la primera página
  openLookup(id_codigo, id_descripcion, 1);
    });
  });


//selector de vistas
  var select = grid.querySelectorAll("select");
  select.forEach(function(item) {
    item.addEventListener("change", function(event) {
  grid.setAttribute("data-view-id",item.value);
  openLookup(id_codigo, id_descripcion, 1);
    });
  });

  var linksPaginacion = grid.querySelectorAll("a.btn-reset");
	linksPaginacion.forEach(function (link) {
		link.addEventListener("click", function (event) {
			event.preventDefault(); // Evitar que el enlace cambie la página
			//una búsqueda por defecto saca la primera página
			search_text = grid.querySelector("input[type='text']");
			search_text.value = "";
			openLookup(id_codigo, id_descripcion, 1);
		});
	});

  var campotexto = grid.querySelector("input[type=text]");
  campotexto.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
      event.preventDefault(); // Evita que el formulario se envíe
    }
  });
 

  var id_tabla =  grid.querySelector("table")?.id;
  addSortingTable(id_tabla);
  
}
