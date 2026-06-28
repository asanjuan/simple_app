<?php
/*
CLASE DE INTERFAZ PARA LA API REST Y SOPORTAR TODOS LOS M�TODOS GET, POST, PATCH, ETC.
CUALQUIER REST API HEREDAR� DE ESTA CLASE E IMPLEMENTAR� LOS M�TODOS 
	ExecutePost
	ExecuteGet
	...
	etc.
Cualquier m�todo no implementado devolver� un error 400 (POST|GET|..ETC) Not Implemented
Para ejecutarlo, s�lo hay que invocar al m�todo Run.
Ejemplo:

	class ProductsApi extends RestApi { ... }
	$obj = new ProductsApi();
	$obj->Run();

*/

class RestApi {
	
	
	public function Run(){
		
		$this->validarCORS();
		$this->validarAcceso();
		
		// Habilita el manejo de solicitudes CORS si es necesario
		//header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		
		$data = null;
		if (COUNT($_POST) >0 || count($_FILES)>0) {
			$data = $_POST;
		}else{
			$tmp = file_get_contents("php://input");
			$data = json_decode($tmp,true);
		}
				
		// Define la l�gica de tu API aqu�
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$this->ExecutePost($data); 
			
		}else if ($_SERVER['REQUEST_METHOD'] === 'GET') {

			$this->ExecuteGet($data);

		}else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

			$this->ExecutePatch($data);
		
		}else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

			$this->ExecutePut($data);
			
		}else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
			
			$this->ExecuteDelete($data);

		}
		
	}
	
	
	public function validarCORS(){
		$list = json_decode( file_get_contents("CORS.json"),true);
	
		if ($list["enableCors"]==true){
			echo $_SERVER['HTTP_ORIGIN'];			
			// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
			// you want to allow, and if so:
			if ( isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'],$list["enabledList"])){
				header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
				/*
				header('Access-Control-Allow-Credentials: true');
				header('Access-Control-Max-Age: 86400');    // cache for 1 day
				*/
			}else{
				$this->return_error(401, "CORS Error");
				
			}
			
		}
	}
	
	public function validarAcceso(){
		$loginManager = new LoginManager();
		
		if ($loginManager->isLoggedIn()){
			//es ok
		}
		else if ($loginManager->testApiKey()){
			//verificar la api key
			//si est� ok no hacemos nada
			
		} else  {
			$this->return_error(401, "Unauthorized");
		}
	}
	
	public function return_error($code, $msg){
			http_response_code($code);
			$response = ['message' => $msg];
			echo json_encode($response);
			die();
	}
	
	public static function validarDatos($jsonArray, $camposRequeridos) {
		$is_ok = true; 
		// Verificar si la decodificaci�n fue exitosa
		if ($jsonArray === null) {
			$is_ok= false;
		}

		// Verificar si los campos requeridos est�n presentes en el array
		foreach ($camposRequeridos as $campo) {
			if (!array_key_exists($campo, $jsonArray)) {
				$is_ok= false;
			}
		}

		if (!$is_ok){
			http_response_code(400);
			$response = ['message' => 'Bad structure', 'fields' => $camposRequeridos];
			echo json_encode($response);
			die();
		}
	}
	
	protected function ExecutePost($data){
		http_response_code(400);
		// Devuelve una respuesta JSON
		$response = ['message' => 'POST Not Implemented'];
		echo json_encode($response);
		die();
		
	}
	protected function ExecuteGet($data){
		http_response_code(400);
		$response = ['message' => 'GET Not Implemented'];
		echo json_encode($response);
		die();
	}
	protected function ExecutePatch($data){
		http_response_code(400);
		$response = ['message' => 'PATCH Not Implemented'];
		echo json_encode($response);
		die();
		
	}
	protected function ExecutePut($data){
		http_response_code(400);
		$response = ['message' => 'PUT Not Implemented'];
		echo json_encode($response);
		die();
		
	}
	protected function ExecuteDelete($data){
		http_response_code(400);
		$response = ['message' => 'DELETE Not Implemented'];
		echo json_encode($response);
		die();
	}
	
}
