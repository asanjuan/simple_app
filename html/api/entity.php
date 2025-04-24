<?php



require_once 'rest_api.php';
include_once '../config.php';
include_once '../utilities.php';
include_once '../database.php';
include_once '../classes/entity_manager.php';
include_once '../classes/loginmanager.php';
include_once '../classes/utility_traits.php';
include_once '../classes/plugin_manager.php';
include_once '../classes/security_manager.php';



class EntityApi extends RestApi {

    protected function ExecutePost($data){

        $controller = $_GET['controller'];
        $messages = [];

        foreach ($data as $record){
            try{

        
                if (isset($record['id'])){
                    dbupdate($controller, $record, "id");
                    $messages [] = ["id" => $record['id'], "message" => "Updated"];
    
                }else{
                    $new_id = dbinsert($controller, $record);
                    $messages [] = ["id" => $new_id, "message" => "Inserted"];
                    
                }
    
            }catch(PDOException $e){
                $messages [] = ["id" => $record['id'], "message" => "Error"];
            }
        }
        
		
        echo json_encode($messages);
	}
	protected function ExecuteGet($data){

        if (isset($_GET['query'])){
            //procesamos una consulta completa tipo json
            $queryJson =  $_GET['query'];
           
            $sql = jsonToSql($queryJson);
            try{
                
                echo json_encode(query($sql));
    
            }catch(PDOException $e){
                http_response_code(500);
                echo json_encode( ["error" =>$e->getMessage()]);
            }

        }else {

            $controller = $_GET['controller'];
            $item = $_GET['item'];
    
            $sql = "select * from $controller";
            if ($item != ""){
                
                $sql = appendcondition($sql, "id = ".quote($item));
            }
            try{
                
                echo json_encode(query($sql));
    
            }catch(PDOException $e){
                http_response_code(500);
                echo json_encode( ["error" => $e->getMessage()]);
            }

        }

        
	}

	protected function ExecuteDelete($data){

	}

}

$entity = new EntityApi();
$entity->Run();