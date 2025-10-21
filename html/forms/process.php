<?php 


require_once '../api/rest_api.php';
include_once '../config.php';
include_once '../utilities.php';
include_once '../database.php';
include_once '../classes/entity_manager.php';
include_once '../classes/loginmanager.php';
include_once '../classes/utility_traits.php';
include_once '../classes/plugin_manager.php';
include_once '../classes/form_manager.php';
include_once '../classes/security_manager.php';

class ProcessDispatcher extends RestApi {
	use Messages;
	use Plugin_list;

   function ExecutePost($data){
       
        $operation = $data['operation'];
        
        switch($operation){
            
            case "select":
                $this->GetFases($data);
                break;
            case "transition":
                $this->Transition($data);
                break;
            default:
                echo json_encode(array("success"=>false, "message"=>"Operación no soportada"));
                break;
        }


    } 

    function GetFases($data){

        $id_proceso = $data['process_id'];
        $item =$data['data_id'];
       
        $proc = EntityManager::GetProcess($id_proceso);
        $run = EntityManager::GetProcessRun($id_proceso , $item);
        $fases = EntityManager::GetEntityProcessStages($id_proceso);
        
        $value = "";
        if (is_array($run)){
            $value = $run['id_fase'];
        }else {
            // No hay proceso iniciado, buscar la fase inicial
            foreach($fases as $f){
                if ($f['tipo_fase'] == 0){
                    $value = $f['id'];
                    $run = [ "id_proceso" => $id_proceso, "id_fase" => $f['id'], "id_registro" => $item ];
                    // Crear el registro de proceso
                    dbinsert("app_proceso_tramites", $run);
                    break;
                }
            }
        }
       
        $transiciones = query("select * from app_transiciones_proceso where id_proceso = ".quote($id_proceso) ." and id_fase_inicio = ".quote($value));


        foreach($fases as $k => $f){
           
            if ($f['id'] == $value){
                $fases[$k]['activo'] = true;
                $orden = $f['orden'];
            } else {
                $fases[$k]['activo'] = false;
            }
        }
        foreach($fases as $k => $f){
           
            if ($f['orden'] < $orden && $f['tipo_fase'] != 1){
                $fases[$k]['completado'] = true;
            } else {
                $fases[$k]['completado'] = false;
            }
        }
        
        echo json_encode([ "fases" => $fases, "current" => $run , "transiciones" => $transiciones] );

    }

    function Transition($data){
        try{

        
            //recogemos los parámetros
            $id_proceso = $data['process_id'];
            $item =$data['data_id'];
            $transition_id = $data['transition_id'];

            //actualizamos el registro de la ejecución del proceso
            $run = EntityManager::GetProcessRun($id_proceso , $item);
            $trans = dbgetbyid("app_transiciones_proceso", $transition_id);
            $run['id_fase'] = $trans['id_fase_fin'];
            dbupdate("app_proceso_tramites", $run);

            //ahora actualizamos el registro del formulario si es necesario
            $proc = EntityManager::GetProcess($id_proceso);
            $entity_name = $proc['entity'];

            //obtenemos la estructura de la entidad
            $estructura = EntityManager::GetEstructura($entity_name);
            $status_field = get_status_field($estructura);

            //obtenemos el valor de la fase
            $fase_data = dbgetbyid("app_fases_proceso", $trans['id_fase_fin']);
            $registro = array();
            $registro['id'] = $item;

            if ($status_field != null ){
                $registro[$status_field['dbcolumn']] = $fase_data['estado_registro'];
            }
            
            if ($proc['campo_entidad'] != ""){
                $registro[$proc['campo_entidad']] = $fase_data['valor'];
            }
            dbupdate( $proc['entity'], $registro);
            
            $this->loadPlugins($entity_name);
            $this->postTransition($item, $trans);

            echo json_encode(array("success"=>true, "message"=>"Transición realizada con éxito"));

         }catch(Exception $e){
           $this->return_error(500, $e->getMessage());
         }
    }

    public function loadPlugins($entidad){
		PluginManager::RegisterForm($this);
		$base_dir = dirname(__DIR__, 1);
		//añadimos los plugins registrados
		$plugin_files = EntityManager::GetPluginFiles($entidad);
		foreach ($plugin_files as $fichero){
			
			//if ($fichero['tipo']==0){
				include $base_dir."/".$fichero['fichero'];
			//}else{
			//	eval ($fichero['code']);
			//}
		}	
	}
	
}

$api = new ProcessDispatcher();
$api->Run();