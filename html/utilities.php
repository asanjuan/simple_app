<?php

include_once 'config.php';



class ConfigSingleton
{
	// Variable est�tica para almacenar la �nica instancia de la clase
	private static $params;


	// M�todo est�tico para obtener la �nica instancia de la clase
	public static function getConfigSingle()
	{
		// Verificar si ya hay una instancia

		if (self::$params === null) {
			// Si no hay instancia, crear una nueva
			self::$params = ConfigSingleton::get_all_config();
		}

		// Devolver la instancia �nica
		return self::$params;
	}

	public static function get_all_config()
	{

		$params = array();
		$q = "select param, value from config";
		$data = query($q);

		foreach ($data as $record) {
			$params[$record['param']] = $record['value'];
		}

		return $params; //devuelve el primer elemento de ese array
	}

	public static function set_param($param, $value){
		$q = "update config set value = '$value' where param = '$param'";
		query($q);
		self::$params[$param] = $value;
	}
}

function new_guid()
{
	return strtoupper(bin2hex(random_bytes(16)));
}


// funciones varias para el funcionamiento del resto de la aplicaci�n

function t($x)
{
	return (utf8_encode($x));
}

function get_URL_BASE()
{
	return get_config('HOME_BASE_URL');

}

function check_URL_BASE(){
	$selected = get_URL_BASE();
	$current = get_URL_index();

	if ($selected != $current){
		ConfigSingleton::set_param('HOME_BASE_URL',$current);
	}
}

function get_URL_index() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME']; // por ejemplo: /miproyecto/index.php
    $path = rtrim(dirname($script), '/\\');
    return "$protocol://$host$path/";
}




function build_URL_Controller_search($controller)
{
	$url = get_URL_BASE();
	$url .= "?controller=" . $controller;
	return $url;
}
function build_URL_Controller_item($controller, $item)
{
	$url = get_URL_BASE();
	$url .= "?controller=" . $controller . "&item=" . $item;
	return $url;
}
function build_URL_Controller_del($controller, $item)
{
	$url = get_URL_BASE();
	$url .= "?controller=" . $controller . "&item=" . $item . "&del";
	return $url;
}
function build_URL_Controller_new($controller)
{
	$url = get_URL_BASE();
	$url .= "?controller=" . $controller . "&new";
	return $url;
}

//genera una tabla html a partir del resultado de una consulta a BD.
function generarTablaHTML($result, $controller = "", $campo_id = "", $access_delete = false, $selectable = true, $image_fields = "")
{
	if (empty($result)) {
		return '<div style="padding:10px;">No hay datos para mostrar.</div>';
	}

	$images = explode(',',$image_fields);

	$random_id = newRandomID();
	$html = '<div class="table-container"><table id="' . $random_id . '" class="tabla-vistosa">';
	// Cabecera de la tabla con los nombres de las columnas
	$html .= '<thead><tr>';
	if ($campo_id != "" && isset($result[0][$campo_id])) {
		$html .= '<th style="width:100px">';
		if ($selectable) $html .= ' <input type="checkbox" name="select_all" onclick="seleccionarTodasLasFilas(\'' . $random_id . '\',this)">  ';
		$html .= '</th>';
	}
	foreach ($result[0] as $columna => $valor) {
		if ($campo_id != $columna) {
			$html .= '<th>' . t($columna) . '</th>';
		}
	}
	if ($access_delete && $campo_id != "" && isset($result[0][$campo_id])) $html .= '<th>Del</th>';

	$html .= '</tr></thead>';
	$html .= '<tbody>';
	// Contenido de la tabla con los valores de cada celda
	foreach ($result as $fila) {

		if ($campo_id != "" && $controller != "" && isset($fila[$campo_id])) {

			$html .= '<tr ondblclick="javascript:redirigir(\'' . build_URL_Controller_item($controller, $fila[$campo_id]) . '\')"><td>';
			if ($selectable) $html .= '<input type="checkbox" name="elementos[]" value="' . $fila[$campo_id] . '"/>';
			//$html .= '<a href="'. build_URL_Controller_item($controller,$fila[$campo_id]) .'" class="boton-enlace"><img src="templates/img/lapiz-blog.svg" class="list-icon" /></a>';
			$html .= '<a href="' . build_URL_Controller_item($controller, $fila[$campo_id]) . '" class="edit-btn"></a>';
			$html .= '</td>';
		} else {
			$html .= '<tr>';
		}
		foreach ($fila as $columna => $valor) {
			if ($campo_id != $columna) {
				if (is_color($valor)) {
					$html .= '<td><span style="display: inline-block; width: 15px; height: 15px; border: 1px solid gray; background-color: ' . $valor . '; border-radius: 50%;"></span></td>';
				} else if ( in_array(  $columna, $images) ){
					$html .= '<td> <img src="'.$valor.'" style="width:100px;height:auto" /> </td>';
				
				} else {
					$html .= '<td>' . format_value($valor) . '</td>';
				}
			}
		}
		if ($access_delete && $campo_id != "" && $controller != "" && isset($fila[$campo_id]))
			$html .= '<td><a  onclick="confirmarYRedirigir(\'Confirma borrar el registro?\',\'' . build_URL_Controller_del($controller, $fila[$campo_id]) . '\')" href="#" class="boton-enlace"><img src="templates/img/papelera-xmark.svg" class="list-icon" /></a></td>';

		$html .= '</tr>';
	}
	$html .= '</tbody>';
	$html .= '</table></div>';
	$html .= '<script> addSortingTable(\'' . $random_id . '\'); </script>';
	return $html;
}


//genera una tabla html a partir del resultado de una consulta a BD.
function generarTablaHTML_mail($result, $controller = "", $campo_id = "", $access_delete = false, $selectable = true)
{
	if (empty($result)) {
		return 'No hay datos para mostrar.';
	}
	
	$random_id = newRandomID();
	$html = '<div class="table-container"><table id="' . $random_id . '" class="tabla-vistosa">';
	// Cabecera de la tabla con los nombres de las columnas
	$html .= '<thead><tr>';
	if ($campo_id != "" && isset($result[0][$campo_id])) {
		$html .= '<th>';
		if ($selectable) $html .= ' <input type="checkbox" name="select_all" onclick="seleccionarTodasLasFilas(\'' . $random_id . '\',this)">  ';
		$html .= 'Editar</th>';
	}
	foreach ($result[0] as $columna => $valor) {
		$html .= '<th>' . t($columna) . '</th>';
	}
	if ($access_delete && $campo_id != "" && isset($result[0][$campo_id])) $html .= '<th>Del</th>';

	$html .= '</tr></thead>';
	$html .= '<tbody>';
	// Contenido de la tabla con los valores de cada celda
	foreach ($result as $fila) {

		if ($campo_id != "" && $controller != "" && isset($fila[$campo_id])) {

			$html .= '<tr ondblclick="javascript:redirigir(\'' . build_URL_Controller_item($controller, $fila[$campo_id]) . '\')"><td>';
			if ($selectable) $html .= '<input type="checkbox" name="elementos[]" value="' . $fila[$campo_id] . '"/>';
			//$html .= '<a href="'. build_URL_Controller_item($controller,$fila[$campo_id]) .'" class="boton-enlace"><img src="templates/img/lapiz-blog.svg" class="list-icon" /></a>';
			$html .= '<a href="' . build_URL_Controller_item($controller, $fila[$campo_id]) . '" >Ver en App</a>';
			$html .= '</td>';
		} else
			$html .= '<tr>';
		
		foreach ($fila as $valor) {
			if (is_color($valor)) {
				$html .= '<td><span style="display: inline-block; width: 10px; height: 10px; background-color: ' . $valor . '; border-radius: 50%;"></span></td>';
			} else {
				$html .= '<td>' . format_value($valor) . '</td>';
			}
		}
		if ($access_delete && $campo_id != "" && $controller != "" && isset($fila[$campo_id]))
			$html .= '<td><a  onclick="confirmarYRedirigir(\'Confirma borrar el registro?\',\'' . build_URL_Controller_del($controller, $fila[$campo_id]) . '\')" href="#" class="boton-enlace"><img src="templates/img/papelera-xmark.svg" class="list-icon" /></a></td>';

		$html .= '</tr>';
	}
	$html .= '</tbody>';
	$html .= '</table></div>';
	$html .= '<script> addSortingTable(\'' . $random_id . '\'); </script>';
	return $html;
}

function is_color($text)
{
	return preg_match('/^#([a-fA-F0-9]{3}|[a-fA-F0-9]{6})$/', $text) === 1;
}


function format_value($valor)
{
	
	if (is_numeric($valor) && is_float($valor+0) ) {
		
		$valor = number_format($valor, 2);
	} elseif (is_numeric($valor)) {
		
		$valor = number_format($valor, 0);

	} elseif (is_string($valor)) {
		// Es una cadena de texto
		// Escapar y formatear seg�n sea necesario
		$valor = strip_tags($valor);
	}
	
	return $valor;
}

function set_default_formulas($estructura, &$datos)
{

	foreach ($estructura as $campo) {
		if ($campo["type"] != "calc" && isset($campo["formula"]) && $campo["formula"] != '') {
			$datos[$campo["dbcolumn"]] = calcular_campo_calculado($campo["formula"], $datos);
		}
	}
}

function generate_form_fields($estructura, $datos, $mostrar_calculados = true)
{

	//tomamos datos del POST para refrescar los datos
	$html = '<table style="width:100%;margin:5px;max-width:700px;">';
	foreach ($estructura as $campo) {

		$val = (isset($datos[$campo["dbcolumn"]]) ? $datos[$campo["dbcolumn"]] : $campo["value"]);
		if ($mostrar_calculados && $campo["type"] == "calc" && isset($campo["formula"]) && $campo["formula"] != '') {
			$val = "valor calculado";
			$val = calcular_campo_calculado($campo["formula"], $datos);
		}
		$disabled = (($campo["disabled"] == 1) ? "disabled" : "");
		$required = (($campo["required"] == 1) ? "required" : "");

		$val = htmlspecialchars($val, ENT_QUOTES);

		if ($campo["type"] != "calc" || ($mostrar_calculados && $campo["type"] == "calc")) {


			$html .= '<tr>';
			//$html .= '<td >'.$campo["label"].'</td>';
			$html .= '<td ><label>' . $campo["label"] . '</label></td>';

			if ($campo["hidden"] == 1) {
				$html .= '<td>  ' . $val . ' <input type="hidden" id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" value="' . $val . '">';
				//$html .= '<td>  <input type="hidden" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'">';

			} else {
				$html .= '<td>';
				//$html .= '<label ><strong>'.$campo["label"].'</strong></label>';
				if (empty($campo["user_control"])) {
					switch ($campo["type"]) {
						case "int":
							$html .= '<input ' . $disabled . ' ' . $required . '  type="number" id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" value="' . $val . '" />';
							break;
						case "decimal":
							$html .= '<input ' . $disabled . ' ' . $required . '  type="number" id="' . $campo["dbcolumn"] . '" step="any" name="' . $campo["dbcolumn"] . '" value="' . $val . '" />';
							break;

						case "text":
							$html .= '<input ' . $disabled . ' ' . $required . '  type="text" maxlength="' . $campo["max"] . '" id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" value="' . $val . '"/>';
							break;
						case "textarea":
							$html .= '<textarea class="' . $campo["class"] . '" ' . $disabled . ' ' . $required . '  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" >' . $val . '</textarea>';

							//$html .= '<input type="text" maxlength="'.$campo["max"].'" id="'.$campo["dbcolumn"].'" name="'.$campo["dbcolumn"].'" value="'.$val.'"/>';
							break;
						case "date":
							$html .= '<input ' . $disabled . ' ' . $required . '   type="date"  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" value="' . $val . '"/>';
							break;
						case "datetime":
							$html .= '<input ' . $disabled . ' ' . $required . '   type="datetime-local"  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" value="' . $val . '"/>';
							break;
						case "password":
							$html .= '<input ' . $disabled . ' ' . $required . '  type="password" maxlength="' . $campo["max"] . '" id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" />';
							break;
						case "color":
							$html .= '<input ' . $disabled . ' ' . $required . '  type="color" id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" value="' . $val . '"/>';
							break;
						case "calc":
							if ($mostrar_calculados) $html .= '<span style="display:block;border:solid 1px #cccccc;padding:5px;font-weight: bold;background-color:#eeeeee;border-radius:5px;">' . $val . '</span>';
							break;
						case "file":
							$html .= '<div><input type="file" name="' . $campo["dbcolumn"] . '" /></div>';
							if (isset($datos[$campo["dbcolumn"]])) {
								$html .= '<div><a href="' . $datos[$campo["dbcolumn"]] . '" download target="_blank">' . basename($datos[$campo["dbcolumn"]]) . '</a></div>';
							} else {
								$html .= '<div>Max ' . ini_get('upload_max_filesize') . '</div>';
							}
							break;
					}
				} else {
					switch ($campo["user_control"]) {
						case "option":
							$html .= '<select id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" ' . $disabled . ' ' . $required . ' >';
							$html .= '<option value=""> - - - </option>';
							foreach ($campo["options"] as $opt) {
								$selected = "";
								if ($opt["value"] == $val) $selected = "selected";
								$html .= '<option value="' . $opt["value"] . '" ' . $selected . '>' . $opt["description"] . '</option>';
							}
							$html .= '</select>';
							break;
						case "lookup":
							$desc_actual = get_lookup_desc($val, $campo["lookup_table"], $campo["lookup_column"],  $campo["lookup_description"]);
							if (!isset($campo["lookup_controller"])) $campo["lookup_controller"] = $campo["lookup_table"]; // por defecto, por si acaso
							$html .= print_lookup_field($campo["dbcolumn"], $campo["lookup_table"], $campo["lookup_column"],  $campo["lookup_description"], $val, $desc_actual, $campo["lookup_controller"],$required);
							break;
						case "image":
							$html .= '<div><input type="file" name="' . $campo["dbcolumn"] . '" /></div>';
							if (isset($datos[$campo["dbcolumn"]])) {
								$html .= '<div style="border:solid 1px #cccccc;text-align:center;background-color:white;padding:5px">';
								$html .= '<img id="'.$campo["dbcolumn"].'_image" class="image_form" src="'.$datos[$campo["dbcolumn"]].'" />';
								$html .= '</div>';
								$html .= '<button type="button" class="image-clear" data-column="'.$campo["dbcolumn"].'" ><i class="fas fa-trash"></i> Borrar</button>';
							} else {
								$html .= '<div>Max ' . ini_get('upload_max_filesize') . '</div>';
							}
							break;
						case "phpcode":

							$html .= '<textarea style="display:none"  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" >' . $val . '</textarea>';
							$html .= '<div class="php_editor" data-control="' . $campo["dbcolumn"] . '" id="' . $campo["dbcolumn"] . '_editor">' . $val . '</div>';

							break;
						case "javascript":

							$html .= '<textarea style="display:none"  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" >' . $val . '</textarea>';
							$html .= '<div class="js_editor" data-control="' . $campo["dbcolumn"] . '" id="' . $campo["dbcolumn"] . '_editor">' . $val . '</div>';

							break;
							case "sql_code":

								$html .= '<textarea style="display:none"  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" >' . $val . '</textarea>';
								$html .= '<div class="sql_editor" data-control="' . $campo["dbcolumn"] . '" id="' . $campo["dbcolumn"] . '_editor">' . $val . '</div>';
	
								break;
							
						case "richtext":

							$html .= '<textarea class="richtext" ' . $disabled . ' ' . $required . '  id="' . $campo["dbcolumn"] . '" name="' . $campo["dbcolumn"] . '" >' . $val . '</textarea>';

							break;
					}
				}


				$html .= '</td>';
			}
			$html .= '</tr>';
		}
	}
	$html .= "</table>";
	return $html;
}

function generate_optionset( $code_field, $description_field, $value_list, $selected_value ){
	$html = '<select >';
	//id="view" name="view"	
	foreach($value_list as $value){
		$selected = "";
		$id = $value[$code_field];
		$nombre = $value[$description_field];
		if ($id == $selected_value) $selected = "selected";
		$html .= '<option value="'.$id .'" '. $selected .'>'.$nombre.'</option>';
		
	}
	$html .= '</select>';
	return $html;
}

function login_test()
{

	$loginManager = new LoginManager();

	$current_url = "http" . (isset($_SERVER['HTTPS']) ? "s" : "") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$param_redirect = base64_encode($current_url);

	if ($loginManager->testApiKey()) {
		//verificar la api key
		//si est� ok no hacemos nada

	} else if (!$loginManager->isLoggedIn()) {
		//Sino, verificar Si el usuario no ha iniciado sesi�n, redirige a la p�gina de inicio de sesi�n.
		header('Location: login.php?r=' . $param_redirect);
		exit;
	}
}


function get_calling_url()
{

	//session_start();

	if (isset($_SESSION['calling_stack'])) {

		$stack = $_SESSION['calling_stack'];
		end($stack);

		// Obtener el �ltimo elemento
		return current($stack);
	} else {

		return $_SERVER['HTTP_REFERER'];
	}
}

function print_calling_stack()
{

	//session_start();

	if (isset($_SESSION['calling_stack'])) {

		$stack = $_SESSION['calling_stack'];
		var_dump($stack);
	}
}


function clear_calling_stack()
{

	//session_start();
	//unset($_SESSION['calling_stack']);
	$_SESSION['calling_stack'] = array();
	//dump($_SESSION['calling_stack']);
}

function get_user_data()
{
	//session_start();

	if (isset($_SESSION['userid'])) {
		return new UserData($_SESSION['userid']);
	}
}

function push_calling_url()
{
	//session_start();
/*
	if (isset($_SESSION['is_form_back']) && $_SESSION['is_form_back'] == true) {
		$_SESSION['is_form_back'] = false;
		return;
	}
*/
	$stack = $_SESSION['calling_stack'];

	if (isset($_SESSION['calling_stack'])) {


		// Obtener el �ltimo elemento
		$ultimoElemento = end($stack);
		
		$ref = $_SERVER['HTTP_REFERER'];
		if (
			strpos($ref, "&new") === false
			&& strpos($ref, "&del") === false
			&& strpos($ref, "back_history") === false
		) {
			if ($ref == $ultimoElemento) {
				//$stack[] = $ref;
				//echo "push ".$ref;
			} else {
				$stack[] = $ref;
				//echo "pushed $ref";
			}
		}
	} else {
		//echo "nEW CALLING STACK";
		$stack = array();
		$stack[] = $_SERVER['HTTP_REFERER'];
	}
	
	$_SESSION['calling_stack'] = $stack;
	//dump($_SESSION['calling_stack']);
}


function pop_calling_url()
{

	//session_start();

	$ultimoElemento = $_SERVER['HTTP_REFERER'];
	//echo($ultimoElemento);

	if (isset($_SESSION['calling_stack'])) {

		$stack = $_SESSION['calling_stack'];
		
		do {
			$tmp = array_pop($stack);
		} while (count($stack) >0 && ( strpos($tmp, "back_history") > 0 || $tmp == $ultimoElemento));

		if (!empty($tmp)){
			$ultimoElemento = $tmp;
		}
		
		$_SESSION['calling_stack'] = $stack;
		$_SESSION['is_form_back'] = true;
	}
	//echo $ultimoElemento;
	return $ultimoElemento;
}

function send_redirect($url)
{

	echo "<script> window.location.href = '" . $url . "' </script>";
}

function print_lookup_field($campo, $tabla, $campo_codigo, $campo_descripcion, $valor_actual, $descripcion_actual, $controller , $required, $disabled="")
{
	
	ob_start();
	
	$url_item = "";
	if ($valor_actual != "") $url_item = build_URL_Controller_item($controller, $valor_actual);
	include 'templates/lookup_field.php';
	return ob_get_clean();
}



function print_grid($view_id, $data_field, $data_value, $grid_id, $grid_enabled=1)
{
	ob_start();
	if ($grid_id == ""){
		$grid_id = newRandomID(5);
	}
		
	$page_size = 15;
	include 'templates/grid.php';

	return ob_get_clean();
}

function print_process($process_id, $item, $process_enabled=1)
{
	ob_start();

	include 'templates/entity_process.php';

	return ob_get_clean();
}


function print_graphic($graphic_id, $data_field, $data_value, $titulo)
{
	ob_start();
	$random_id = newRandomID(5);
	include 'templates/graphics_template.php';

	return ob_get_clean();
}

function get_lookup_desc($valor, $tabla, $campo_codigo, $campo_descripcion)
{
	global $conn;
	$retorno = "";
	// Preparar la instrucci�n SQL de inserci�n con una consulta preparada
	$sql = "Select $campo_descripcion as descripcion from $tabla where $campo_codigo = :valor";

	$stmt = $conn->prepare($sql);

	// Verifica si la consulta preparada es v�lida
	if ($stmt) {
		// Vincular par�metros y ejecutar la consulta preparada
		$stmt->bindParam(":valor", $valor);
		$stmt->execute();
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($data) > 0) {
			$retorno = $data[0]["descripcion"];
		}
	}
	return $retorno;
}

function newRandomID($longitud = 10)
{

	$caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$claveApi = '';

	for ($i = 0; $i < $longitud; $i++) {
		$indiceAleatorio = mt_rand(0, strlen($caracteres) - 1);
		$claveApi .= $caracteres[$indiceAleatorio];
	}

	return $claveApi;
}

function generarApiKey($longitud)
{
	$caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$claveApi = '';

	for ($i = 0; $i < $longitud; $i++) {
		$indiceAleatorio = mt_rand(0, strlen($caracteres) - 1);
		$claveApi .= $caracteres[$indiceAleatorio];
	}

	return $claveApi;
}


function calcular_campo_calculado($expresion, $datos)
{
	// obtendremos una sql con variables del con el formato {campo} apuntando a alguno de los datos del formulario
	if ($datos && $datos['id']!=""){
		foreach ($datos as $campo => $valor) {

			$expresion = str_replace("{" . $campo . "}", quote($valor), $expresion);
		}
		//trace($expresion);

		try {

			$data = query1($expresion);
		} catch (PDOException $e) {
			trace("Error en la conexi�n: " . $e->getMessage());
		}

		
		return reset($data); //devuelve el primer elemento de ese array
	}else {
		return 0;
	}

	
}


function get_config($param)
{
	/*
	$q = "select value from config where param = " . quote ($param);
	$data = query1($q);
	
	return reset($data); //devuelve el primer elemento de ese array
	*/
	$params = ConfigSingleton::getConfigSingle();
	return $params[$param];
}

function get_all_config()
{
	/*
	$params = array();
	$q = "select param, value from config";
	$data = query($q);
	
	foreach($data as $record){
		$params[$record['param']] = $record['value'] ;
		
	}
	
	return $params; //devuelve el primer elemento de ese array
	*/
	return ConfigSingleton::getConfigSingle();
}

function sanitizeDecimal($input)
{

	// Si se permiten decimales, utiliza FILTER_SANITIZE_NUMBER_FLOAT en lugar de FILTER_SANITIZE_NUMBER_INT
	return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

/*
function precio($precioTexto) {
	//$precioTexto = str_replace(',', '.', $precioTexto);
	//$precioNumerico = preg_replace('/[^0-9.]/', '', $precioTexto);
	
	// Convertir a n�mero decimal
	$precioDecimal = round( floatval($precioNumerico) ,2);
	
	return $precioDecimal;
}
*/
function precio($texto)
{
	// Eliminar todo excepto d�gitos, puntos y comas
	$texto = preg_replace("/[^0-9,.]/", "", $texto);

	// Reemplazar las comas por puntos para el separador decimal
	$texto = str_replace(",", ".", $texto);

	// Si hay m�s de un punto decimal, mantener solo el �ltimo
	$partes = explode(".", $texto);
	if (count($partes) > 2) {
		$texto = implode("", array_slice($partes, 0, -1)) . "." . end($partes);
	}

	// Convertir el texto en un n�mero float
	$numero = floatval($texto);

	return $numero;
}




function postRequest($url, $data, $securityToken)
{


	$headers = [
		"Securitytoken: $securityToken",
		"Content-Type: application/json"
	];

	$ch = curl_init($url);

	// Configuramos cURL para una solicitud POST
	curl_setopt($ch, CURLOPT_POST, 1);

	if ($data !== null) {
		// Codificamos el arreglo como JSON
		$json_data = json_encode($data);

		// Establecemos las opciones para la solicitud POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	}
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// Configuramos para recibir la respuesta en lugar de imprimir
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Ejecutamos la solicitud
	$response = curl_exec($ch);

	// Verificamos si hay errores
	if (curl_errno($ch)) {
		echo 'Error: ' . curl_error($ch);
	}

	// Cerramos la sesi�n cURL
	curl_close($ch);

	return json_decode($response, true);
}

function getRequest($url, $securityToken)
{


	$headers = [
		"Securitytoken: $securityToken",
		"Content-Type: application/json"
	];

	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// Ejecutamos la solicitud
	$response = curl_exec($ch);

	// Verificamos si hay errores
	if (curl_errno($ch)) {
		echo 'Error: ' . curl_error($ch);
	}
	//echo $response;
	// Cerramos la sesi�n cURL
	curl_close($ch);

	return json_decode($response, true);
}


function eliminarAtributosClassStyle($html)
{
	// Eliminar el atributo 'class' y su contenido
	$regex = '/class=["\'][^"\']*["\']/';
	$html = preg_replace($regex, '', $html);
	// Eliminar el atributo 'style' y su contenido
	$regex = '/style=["\'][^"\']*["\']/';
	$html = preg_replace($regex, '', $html);

	return $html;
}

function obtenerDominioDeURL($url)
{
	$url_info = parse_url($url);

	if (isset($url_info['scheme']) && isset($url_info['host'])) {
		// Construir la URL del dominio
		$domain = $url_info['scheme'] . '://' . $url_info['host'];
		return $domain;
	}

	return "";
}

function comienzaCon($texto, $inicio)
{
	return strpos($texto, $inicio) === 0;
}
