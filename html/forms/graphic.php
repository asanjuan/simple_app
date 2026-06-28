<?php 


require_once '../api/rest_api.php';
include_once '../config.php';
include_once '../utilities.php';
include_once '../database.php';
include_once '../classes/entity_manager.php';
include_once '../classes/loginmanager.php';
include_once '../classes/utility_traits.php';



class GraphicData extends RestApi {
	
	
	
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
      field: datafield,
      value: datavalue,
      graphicid: id,

    };

*/

	public function execute_select($data){
		

		$graphic_id = $data["graphicid"];
		

		$obj = EntityManager::GetGraphic($graphic_id);
		
		$sql = $obj['query'];
		if (isset($data["field"]) && $data["field"] != ""){
			$filter = $data["field"] ."=" . quote($data["value"]);
			$sql =  appendcondition($sql, $filter);
		}
				
		$orderby = $obj['order_by'];
		if ($orderby != "") $sql .= " order by " .$orderby;
		
		$datos = query($sql);
		$result["label_field"]=  $obj["label_field"];
		$result["data_field"]= explode(",",$obj["data_field"]);
		$result["type"] = $obj["tipo"];
		$result["title"] = $obj["name"];
		$result["data"] = $datos;

		echo json_encode($result);
	}
	

}

$obj = new GraphicData();
$obj->Run();



