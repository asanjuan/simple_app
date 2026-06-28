<?php

//require_once 'database.php';
//require 'classes/security_manager.php';

function getHeadersArray() {
    $headers = array();

    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $headerName = str_replace('HTTP_', '', $key);
            $headerName = str_replace('_', ' ', $headerName);
            $headerName = ucwords(strtolower($headerName));
            $headerName = str_replace(' ', '-', $headerName);

            $headers[$headerName] = $value;
        }
    }

    return $headers;
}


class LoginManager {
   
	public function testApiKey(){
		global $conn;
		
		$headers = getHeadersArray();
		$token = "";
		if (isset($headers["Apikey"])){
			$token = $headers["Apikey"];
		}else {
			return false;
		}
		
		//consultamos en base de datos la api key
		
		$sql = "SELECT * from apikeys WHERE apikey = :token ";
		
		$stmt = $conn->prepare($sql);
		
		$stmt->bindValue( ':token',$token  );
		
		$stmt->execute();
		
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		
		
		if ( $data ){
			return true;
		}
		return false;
	}
	
    public function login($username, $password) {
        // Aquí deberías realizar la autenticación con una base de datos o una fuente segura.
        // Por simplicidad, usaremos un array para verificar el usuario y la contraseña.
		
		//echo $username, $password;
		try{
			$db = get_DB();
			
			$sql = "SELECT * from usuarios WHERE login = :login AND pwd = :pwd ";
			
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue( ':login',$username  );
			$stmt->bindValue( ":pwd", md5($password)  );
			
			$stmt->execute();
			
			$data = $stmt->fetch(PDO::FETCH_ASSOC);
			
			
			if ( $data ){
				//session_start();
				$_SESSION['username'] = $username;
				$_SESSION['userid'] = $data["id"];
				//$_SESSION['user_is_admin'] = SecurityManager::UserIsAdmin($data["id"]);
				return true;
			}
		}catch (PDOException $e) {
			
			// Manejo de errores en caso de que falle la conexión o la consulta
			//echo "Error: ";
		}		
        return false;
    }

    public function isLoggedIn() {
        // Verificar si el usuario ha iniciado sesión comprobando la existencia de la variable de sesión 'username'.
		//session_start();
        return isset($_SESSION['username']);
    }

    public function logout() {
        // Cerrar sesión (eliminar la variable de sesión 'username').
        //session_start();
        session_unset();
        session_destroy();
    }
	
	
}


class UserData {
	
	public $data;
	public $userid;

	function __construct($idusuario){
		
		$this->data = $this->load($idusuario);
		$this->userid = $idusuario;
	}

	function save(){
		
		global $conn;
		$sql = "delete from user_data where id_usuario = ". $this->userid;
		query($sql);
		
		$sql = "INSERT INTO user_data (id_usuario, json_data) VALUES (:id, :data)";
		$stmt = $conn->prepare($sql);
		
		// Verifica si la consulta preparada es válida
		if ($stmt) {
			
			$stmt->bindValue( ":id", $this->userid  );
			$stmt->bindValue( ":data", json_encode($this->data)  );
			
			$stmt->execute();
		}
	}


	function load($userid) {
		$sql = "select json_data from user_data where id_usuario = ". $userid;
		$datos = query1($sql);
		if (isset($datos['json_data']) ) {
			return json_decode($datos['json_data'],true);
		}else{
			return array();
		}	

	}


}
