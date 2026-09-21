<?php

require dirname(__DIR__).'/autoload.php';



class EntityApi extends RestApi {

    protected function ExecutePost($data){

        $controller = $_GET['controller'];
        $messages = [];
        if (!isValidTableName($controller) ){
            $this->returnError(400,"Bad Request");
        }
        foreach ($data as $record){
            try{

        
                if (isset($record['id'])){
                    //upsert
                    if (dbexists($controller,$record['id'])){
                        dbupdate($controller, $record, "id");
                        $messages [] = ["id" => $record['id'], "message" => "Updated" ];
                    }else{
                        dbinsert($controller, $record,  true); //verbatim copy id included
                        $messages [] = ["id" => $new_id, "message" => "Inserted" ];
                    }
                    
                }else{
                    $new_id = dbinsert($controller, $record);
                    $messages [] = ["id" => $new_id, "message" => "Inserted" ];
                    
                }
    
            }catch(PDOException $e){
                $messages [] = ["id" => $record['id'], "error" => $e, "record" => $record ];
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
            if (!isValidTableName($controller) ){
                $this->return_error(400,"Bad Request");
            }

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