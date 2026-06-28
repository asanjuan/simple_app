<?php

require_once 'rest_api.php';
include_once '../config.php';
include_once '../utilities.php';
include_once '../database.php';
include_once '../classes/loginmanager.php';



class CypherApi extends RestApi {

    protected function ExecutePost($data){

        $this->validarDatos($data, ["method","message"]);
        
        $method = $data['method'] ?? '';
        $message = $data['message'] ?? '';

        try{
            if ($method == "cypher"){
                $key = __CYPHERKEY__ ;
                $cyphered = cypherMessageAES($message, $key);
                //echo $cyphered;
                //
                echo json_encode(["message" => $cyphered]);  
                die();
            } else if ($method == "decypher"){
                $key = __CYPHERKEY__ ;
                $decyphered = decypherMessageAES($message, $key);
                
                echo json_encode(["message" => $decyphered]);
                //echo $decyphered ;

                die();
            } else {
                $this->return_error(400, "Bad Request: Unknown method");
            }
        
        }catch(Exception $e){
            $this->return_error(500, $e->getMessage());     
        }   
        
	}


}

$entity = new CypherApi();
$entity->Run();