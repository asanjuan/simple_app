<?php
include_once 'config.php';

$db_servername = "db";
$db_username = "root";
$db_password = "pass";
$dbname = "simple_app";
$conn = get_DB();



function get_DB(){
	
	global $db_servername, $db_username, $db_password, $dbname, $conn;
	$dsn = 'mysql:host='.$db_servername.';dbname='.$dbname;

	try {
		$conn = new PDO($dsn, $db_username, $db_password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return $conn;
		
	} catch (PDOException $e) {
		echo "Error en la conexi�n: " . $e->getMessage();
	}
		
}

function trace($sql){
	echo "<pre>";
	echo $sql ;
	echo "</pre>";

}

//************************************************************************************************
// consulta para recuperar datos de BD
//************************************************************************************************
function query($sql){
	global $conn;
	if (__DEBUGSQL__) trace($sql); 
	
	$consulta = $conn->prepare($sql);
	$consulta->execute();
	return  $consulta->fetchAll(PDO::FETCH_ASSOC);

}

function query1($sql){
	global $conn;

	if (__DEBUGSQL__) trace($sql); 
	
	$consulta = $conn->prepare($sql);
	$consulta->execute();
	return  $consulta->fetch(PDO::FETCH_ASSOC);

}

function count_records($sql){
	global $conn;
	
	$sql = "select count(0) as total from ( $sql ) as V";
	//if (__DEBUGSQL__) trace($sql); 
	$data = query1($sql);
	
	return $data["total"];

}


function quote($txt, $type="text"){
	global $conn;
	if ($type == "text" || $type == "date")
		return $conn->quote($txt);
	else
		return $txt;
}

function mask($valor,$tipo){
	if ($tipo == "password") return md5($valor);
	else return $valor;

}

function appendcondition($sql,$condition){
	//trace($sql);
	//trace($condition);
	
	if (strpos( strtolower($sql), "having " ) >0) {
		return $sql .= " and (" . $condition . ")";
		
	}else if (strpos( strtolower($sql), "where " ) >0) {
		return $sql .= " and (" . $condition . ")";
		
	} else if (strpos( strtolower($sql), "group by " ) >0) {
		return $sql .= " having (" . $condition . ")";
		
	} else {
		return $sql .= " where (" . $condition . ")";
	}
}


function appendOR($exp,$condition){
	if ($exp != "")
		return $exp . " OR " . $condition ;
	else 
		return $condition;
	
	
}


/*
	funci�n gen�rica, toma una tabla y un array e inserta los datos del array en la tabla 
	emparejando los nombres de las columnas con el array
*/
function dbinsert($tabla, $datos){
	global $conn;
	
	$datos = array_map(function($value) {
                        return $value === "" ? NULL : $value;
                     }, $datos);
	
	$new_id = new_guid(); 
	$datos['id'] = $new_id;
	
	$campos = array_keys($datos);
	$fields = ""; $params = "";
	//var_dump($datos);

					 
	foreach($campos as $campo){		
			$fields .= $campo.",";
			$params .= ":".$campo.",";

	}
	
	$fields = substr($fields, 0, -1);
	$params = substr($params, 0, -1);
	
	$sql = "INSERT INTO ".$tabla." (" . $fields .") VALUES (".$params.")";

	$stmt = $conn->prepare($sql);
	
	if (__DEBUGSQL__) trace($sql);
	
	// Verifica si la consulta preparada es v�lida
	if ($stmt) {
		// Vincular par�metros y ejecutar la consulta preparada
		foreach($datos  as $campo => $val){
				$stmt->bindValue( ":".$campo, $val  );
		}
		
		$stmt->execute();
		
		return $new_id;
		
	}
	return false;
}

function dbgetbyid($tabla,$id){
	$sql = "Select * from ".$tabla." where id =" . quote($id);	
	$datos = query1($sql); 
	return $datos;
}

function dbupdate($tabla, $datos, $key ="id"){
	global $conn;
	
	// Preparar la instrucci�n SQL de inserci�n con una consulta preparada
	$sql = "update ".$tabla." set ";
	
	$datos = array_map(function($value) {
					return $value === "" ? NULL : $value;
				 }, $datos);
					 
	$campos = array_keys($datos);
	foreach($campos  as $campo){
		
		$sql .= "`".$campo."` = :".$campo . ",";
		
	}
	$sql = substr($sql, 0, -1);
	$sql .= " where " . $key ." = :" .$key; 
	
	//echo $sql;
	if (__DEBUGSQL__) trace($sql);
	$stmt = $conn->prepare($sql);
	
	// Verifica si la consulta preparada es v�lida
	if ($stmt) {
		// Vincular par�metros y ejecutar la consulta preparada
		foreach($datos  as $campo => $val){
				$stmt->bindValue( ":".$campo, $val  );													
		}
		
		$stmt->execute();
		
		return true;
		
	}
	return false;

	
}


function dbdelete($tabla, $datos){
	global $conn;
	
	// Preparar la instrucci�n SQL de inserci�n con una consulta preparada
	$sql = "delete from ".$tabla;
	$campos = array_keys($datos);
		
	foreach($campos  as $campo){
		$sql = appendcondition($sql, $campo." = :".$campo );		
	}

	
	//echo $sql;
	if (__DEBUGSQL__) trace($sql);
	$stmt = $conn->prepare($sql);
	
	// Verifica si la consulta preparada es v�lida
	if ($stmt) {
		// Vincular par�metros y ejecutar la consulta preparada
		foreach($datos  as $campo => $val){
			$stmt->bindValue( ":".$campo, $val  );							
		}
		
		$stmt->execute();
		
		return true;
		
	}
	return false;

}


function nextSequence($nombre){
	$sql = "select * from app_numeraciones where nombre ='$nombre'";
	$data = query1($sql);
	$value = 1;
	$prefix = "";
	$longitud = 5;
	 
	if (!empty($data)){
		//usamos una numeración existente
		$value = $data['siguiente'];
		$prefix = $data['prefijo'];
		$data['siguiente'] = $value +1;
		dbupdate("app_numeraciones", $data);

	}else {
		$data = [ 
			"nombre" => $nombre,
			"prefijo" => "",
			"anio" => null,
			"siguiente" => ($value +1),
			"longitud" => $longitud
		];
		dbinsert("app_numeraciones", $data);
	}

	$formated_value = str_pad($value,$longitud,'0',STR_PAD_LEFT );
	if ($prefix != ""){
		$formated_value = $prefix . "-".$formated_value;
	}

	return $formated_value;
}


function nextYearSequence($nombre,$year){
	$sql = "select * from app_numeraciones where nombre ='$nombre' and anio=$year";
	$data = query1($sql);
	$value = 1;
	$prefix = "";
	$longitud = 5;
	 
	if (!empty($data)){
		//usamos una numeración existente
		$value = $data['siguiente'];
		$prefix = $data['prefijo'];
		$data['siguiente'] = $value +1;
		dbupdate("app_numeraciones", $data);

	}else {
		$data = [ 
			"nombre" => $nombre,
			"prefijo" => "",
			"anio" => $year,
			"siguiente" => ($value +1),
			"longitud" => $longitud
		];
		dbinsert("app_numeraciones", $data);
	}

	$formated_value = str_pad($value,$longitud,'0',STR_PAD_LEFT );
	if ($prefix != ""){
		$formated_value = $prefix . $year. "-".$formated_value;
	}

	return $formated_value;
}

/*
CONVERSIONES DE JSON A SQL PARA LA API
*/
function sqlToJson($query) {
    $query = preg_replace('/\s+/', ' ', trim($query)); // Normaliza los espacios y saltos de línea
    
    $parsedQuery = [
        "select" => [],
        "from" => ["table" => "", "alias" => ""],
        "joins" => [],
        "where" => "",
        "groupBy" => [],
        "having" => "",
        "orderBy" => [],
        "limit" => null
    ];
    
    if (preg_match('/SELECT( DISTINCT)? (.+?) FROM ([^ ]+)(?: AS ([^ ]+))?/i', $query, $matches)) {
        $parsedQuery["distinct"] = strtoupper(trim($matches[1])) === "DISTINCT";
        $parsedQuery["select"] = array_map('trim', explode(',', $matches[2]));
        $parsedQuery["from"] = [
            "table" => trim($matches[3]),
            "alias" => isset($matches[4]) ? trim($matches[4]) : ""
        ];
    }
    
    if (preg_match_all('/(INNER|LEFT|RIGHT) JOIN ([^ ]+)(?: AS ([^ ]+))? ON ([^ ]+\s*=\s*[^ ]+)/i', $query, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $parsedQuery["joins"][] = [
                "type" => strtoupper($match[1]),
                "table" => trim($match[2]),
                "alias" => isset($match[3]) ? trim($match[3]) : "",
                "on" => trim($match[4])
            ];
        }
    }
    
    if (preg_match('/WHERE (.+?)( GROUP BY| ORDER BY| LIMIT|$)/i', $query, $matches)) {
        $parsedQuery["where"] = trim($matches[1]);
    }
    
    if (preg_match('/GROUP BY (.+?)( HAVING| ORDER BY| LIMIT|$)/i', $query, $matches)) {
        $parsedQuery["groupBy"] = array_map('trim', explode(',', $matches[1]));
    }
    
    if (preg_match('/HAVING (.+?)( ORDER BY| LIMIT|$)/i', $query, $matches)) {
        $parsedQuery["having"] = trim($matches[1]);
    }
    
    if (preg_match('/ORDER BY (.+?)( LIMIT|$)/i', $query, $matches)) {
        $parsedQuery["orderBy"] = array_map('trim', explode(',', $matches[1]));
    }
    
    if (preg_match('/LIMIT (\d+)/i', $query, $matches)) {
        $parsedQuery["limit"] = (int) $matches[1];
    }
    
    return json_encode($parsedQuery, JSON_PRETTY_PRINT);
}



function jsonToSql($jsonQuery) {
    $queryArray = json_decode($jsonQuery, true);
    
    $sql = "SELECT ";
    if (!empty($queryArray["distinct"])) {
        $sql .= "DISTINCT ";
    }
    $sql .= implode(", ", $queryArray["select"]);
    $sql .= " FROM " . $queryArray["from"]["table"];
    if (!empty($queryArray["from"]["alias"])) {
        $sql .= " AS " . $queryArray["from"]["alias"];
    }
    
    foreach ($queryArray["joins"] as $join) {
        $sql .= " {$join["type"]} JOIN {$join["table"]}";
        if (!empty($join["alias"])) {
            $sql .= " AS {$join["alias"]}";
        }
        $sql .= " ON {$join["on"]}";
    }
    
    if (!empty($queryArray["where"])) {
        $sql .= " WHERE " . $queryArray["where"];
    }
    
    if (!empty($queryArray["groupBy"])) {
        $sql .= " GROUP BY " . implode(", ", $queryArray["groupBy"]);
    }
    
    if (!empty($queryArray["having"])) {
        $sql .= " HAVING " . $queryArray["having"];
    }
    
    if (!empty($queryArray["orderBy"])) {
        $sql .= " ORDER BY " . implode(", ", $queryArray["orderBy"]);
    }
    
    if (!empty($queryArray["limit"])) {
        $sql .= " LIMIT " . $queryArray["limit"];
    }
    
    return $sql;
}



function subtotals($entity_name, $column_array, $related_field, $id){
    
    
    $query = "Select ";
    $aux = [];
    
    foreach($column_array as $col){
        $aux[] = "sum($col) as $col";
    }
    $query .= join(",",$aux);
    $query .= " FROM $entity_name WHERE $related_field = '$id'";
    
    return query1($query);
    
}