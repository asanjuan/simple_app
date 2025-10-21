
//al cargar la página, cargamos todos los grids
document.addEventListener("DOMContentLoaded", function() {
    // Cargar datos del subgrid cuando la página esté cargada
    cargarTodosGraficos();
  });
  
  function cargarTodosGraficos() {

   // Agregar event listeners para los enlaces de paginación (si los tienes)
    var gridlist = document.querySelectorAll("div.datagraphics");
    gridlist.forEach(function(grid) {
      
      cargarGrafico(grid);
      
    });
   
  }
  
  function cargarGrafico(grid){
    
    let canvas = grid.querySelector("canvas");
    let url = grid.getAttribute("data-gridurl");
    let datafield = grid.getAttribute("data-field");
    let datavalue = grid.getAttribute("data-value");
    let graphicid = grid.getAttribute("data-graphic-id");

    // Configurar los datos del filtro
    var filtro = {
	  
      field: datafield,
      value: datavalue,
      graphicid: graphicid

    };
  
    // Realizar una solicitud fetch para obtener los datos del subgrid
    fetch(url, {
      method: "POST",
      body: JSON.stringify(filtro)
    })
      .then(response => response.json())
      .then(objresponse => {
        //console.log(objresponse);
        
        let label_items = objresponse.data.map(row => row[objresponse.label_field]);
        let datasets = [];
        objresponse.data_field.forEach( item => {
            item = item.trim();
            datasets.push( { data: objresponse.data.map(row => row[item]), label: item, hoverOffset:4 } );
        }
        );

        //let data_items = objresponse.data.map(row => row[objresponse.data_field]);
        let graphic_data = {
            labels: label_items,
            datasets: datasets 
            /*[
                {data: data_items, label: objresponse.data_field, hoverOffset:4},
                {data: data_items, label: 'segunda trama', hoverOffset:4}
            ]*/
        };

        let cfg = {
            type: objresponse.type,
            data: graphic_data,
            options: {
               responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: objresponse.title
                    }
                }
            }
        }
        
        //console.log(cfg);
        
        let draw = new Chart(canvas, cfg);
     
    }
    ).catch(error => console.error('Error al cargar el gráfico:', error));
    

  }
  