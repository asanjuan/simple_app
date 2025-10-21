<?php 


require_once '../api/rest_api.php';
include_once '../config.php';
include_once '../utilities.php';
include_once '../database.php';
include_once '../classes/entity_manager.php';
include_once '../classes/loginmanager.php';
include_once '../classes/utility_traits.php';



class Form_lookup extends RestApi {
	
	use Messages;
	use Plugin_list;
	
	protected $buttons;
	
	protected $this_controller = "";
	protected $key_field = "id";
	protected $view_list;
	protected $view_id_selected;
	protected $lookup_id_field;
	protected $lookup_id_desc;
	protected $campo_principal;

	
	protected function ExecutePost($data){
		
		$this->execute_select($data); 
		
	}
	
/*
var filtro = {
	  operation: operation,
      // Define tu filtro aquí, por ejemplo:
      field: datafield,
      value: datavalue,
      pagesize: pagesize,
      page: page,
      view: view,
	  table: tabla,
	  search_text: search_text
    };

*/

	public function execute_select($data){
		
		
		if (isset($data["field"]) && $data["field"] != ""){
			$filter = $data["field"] ."=" . quote($data["value"]);
		}
		
		$page = 1;
		if (isset($data["page"]) && is_numeric( $data["page"])){
			$page = $data["page"];
		}
		$items_per_page = 10;

		$view_id = $data["view"];
		$this->view_id_selected = $view_id;

		$search_text = $data["search_text"];
				
		
		$metadata = EntityManager::GetEntity($data['table']);
		
		$this->view_list = EntityManager::GetVistasLookup($metadata['id']);
		$this->view_id_selected = $this->view_list[0]['id'];
		

		if (isset($data["view"]) && $data["view"]!= ""){
			$this->view_id_selected = $data["view"];
			
		}

		$obj = EntityManager::GetVista($this->view_id_selected);
				
		$imagefield = $obj['image_fields'];
		
		$sql = $obj['query'];
		$search_fields = $obj['search_fields'];
		
		if ($search_fields != "" && $search_text != ""){
			$filters = explode(",", $search_fields);
			$search_condition = "";

			foreach($filters as $search_field){
				$search_condition = appendOR($search_condition, $search_field . " like '%".$search_text."%'");
				//trace($search_condition);
			}
			$sql =  appendcondition($sql, $search_condition);

		}
		
		$this->this_controller = $metadata['entity'];
		$this->campo_principal = $metadata['campo_principal'];
		$this->key_field = "id";
		$this->lookup_id_field = $data['lookup_id_field'];
		$this->lookup_id_desc = $data['lookup_id_desc'];
		
		$orderby = $obj['order_by'];


		echo $this->list_controller($sql, $filter, $search_text, $orderby, $page, $items_per_page ,$imagefield );
	}
	
	
	public function list_controller($sql, $filter,$search_text, $orderby, $page, $items_per_page, $imagefield){

		global $conn;
		
			
		$offset = ($page - 1) * $items_per_page;
		if ($filter != "")
			$sql =  appendcondition($sql, $filter);
		
		
		$total_registros = count_records($sql); //obtenemos un total de los registros
		$total_pages = ceil($total_registros / $items_per_page);
		if ($page > $total_pages) $page = 1; // por si acaso
		
		if ($orderby != "") $sql .= " order by " .$orderby;
		
		$sql .= " LIMIT  $offset, $items_per_page";
				
		$datos = query($sql); //añadir los filtros que falten
		
		$key_field = "id";
		//if (!$this->access_update) $key_field = "";
		
		//$listado_html = generarTablaHTML($datos, "", "", false,false,$imagefield);
		$listado_html = $this->generarTablaHTML_lookup($datos, $this->lookup_id_field, $this->lookup_id_desc, $imagefield);	
		
		$opt_vistas = generate_optionset("id","name",$this->view_list, $this->view_id_selected );
		$vistas_html = '<div> '.$opt_vistas.'</div>';


		$busqueda = '<div class="grid-search-group"><input type="text" name="q" value="'.$search_text.'">';
		$busqueda .=  '<a class="boton-enlace btn-search" href="#" data-operation="search"> <i class="fa-solid fa-search"></i> <span> Buscar </span> </a>  ';
		$busqueda .=  '<a class="boton-enlace btn-reset" href="#" data-operation="search"><i class="fa-solid fa-square-xmark"></i> <span> Reset </span></a> '; 
		$busqueda .= '</div>'; 
		
		$grid_toolbar = '<div class="gridtoolbar">'.$vistas_html.$busqueda.'</div>';
		
		$paginacion = "";
		if ($total_pages > 1){
			if ( $page > 1) {
				$paginacion .= '<a class="boton-enlace grid-pagina" href="#" data-page="'.($page-1).'"> << Anterior </a> ';
			}
			if ( $page < $total_pages) {
				$paginacion .= '<a class="boton-enlace grid-pagina" href="#" data-page="'.($page+1).'"> Siguiente >> </a> ';
			}
		}
		$total = "<strong>  $page / $total_pages ($total_registros registros)</strong>";
		return  $grid_toolbar . $listado_html . $paginacion .$total;
		
	}

	function generarTablaHTML_lookup($result, $campo_id, $campo_desc, $image_fields = "")
	{
		if (empty($result)) {
			return '<div style="padding:10px;">No hay datos para mostrar.</div>';
		}

		$images = explode(',',$image_fields);

		$random_id = newRandomID();
		$html = '<div class="table-container"><table id="' . $random_id . '" class="tabla-vistosa">';
		// Cabecera de la tabla con los nombres de las columnas
		$html .= '<thead><tr>';
		$html .= '<th style="width:100px">Select </th>';
			
		
		foreach ($result[0] as $columna => $valor) {
			if ($columna != "id"){
				$html .= '<th>' . t($columna) . '</th>';
			}
		}
		
		$html .= '</tr></thead>';
		$html .= '<tbody>';
		// Contenido de la tabla con los valores de cada celda
		foreach ($result as $fila) {
			
			$html .= '<tr> <td><button onclick="lookupElementClick(\''.$campo_id.'\', \''.$campo_desc.'\', \''.$fila['id'].'\', \''.$fila[$this->campo_principal].'\')" > Select </button></td>';
			
			foreach ($fila as $columna => $valor) {
				if ($columna != "id") {
					if (is_color($valor)) {
						$html .= '<td><span style="display: inline-block; width: 15px; height: 15px; border: 1px solid gray; background-color: ' . $valor . '; border-radius: 50%;"></span></td>';
					} else if ( in_array(  $columna, $images) ){
						$html .= '<td> <img src="'.$valor.'" style="width:100px;height:auto" /> </td>';
					
					} else {
						$html .= '<td>' . format_value($valor) . '</td>';
					}
				}
			}
			
			$html .= '</tr>';
		}
		$html .= '</tbody>';
		$html .= '</table></div>';
		$html .= '<script> addSortingTable(\'' . $random_id . '\'); </script>';
		return $html;
	}
	
	
}
$obj = new Form_lookup();
$obj->Run();



