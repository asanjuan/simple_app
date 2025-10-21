<?php

include_once 'config.php';
include_once 'utilities.php';
include_once 'database.php';
require_once 'classes/loginmanager.php';
include_once 'classes/entity_manager.php';
include_once 'classes/form_manager.php';
include_once 'classes/security_manager.php';
include_once 'classes/report_manager.php';

require_once 'lib/mpdf/vendor/autoload.php';

login_test();

$template_id = $_GET['template_id'];
$item =  $_GET['item'];

// Obtenemos el contenido de la plantilla
$template = query1("select * from app_report_templates where id = " . quote($template_id));


//obtenemos los datos del informe
$data = [];

$consultas =  query("select * from app_report_queries where id_template = " . quote($template_id));
foreach ($consultas as $consulta) {
	$nombre = $consulta['nombre'];
	$sql = $consulta['query'];
	$sql = str_replace("{item}", quote($item), $sql);
	//trace($sql);

	if ($consulta['resultado'] == "unique") {
		$data[$nombre] = query1($sql);
	} else {
		$data[$nombre] = query($sql);
		//dump($data[$nombre]);
	}
}

$engine = new TemplateEngine($template['template']);
//estilos básicos para tablas y ancho del informe. De momento solo formato vertical.
$reportcontent = "
    <html>
      <head>
        <style>
          body { max-width:800px; font-family: Arial; background-color:white;padding:20px; font-size:10px; }
		  td {padding:8px;min-height:48px;}
		  @media print {
			table td {
				-webkit-print-color-adjust: exact !important; /* Chrome, Edge, Safari */
				print-color-adjust: exact !important;        /* Estándar */

			}
		}
        </style>
      </head>
      <body>" . $engine->render($data) . "</body>
    </html>";
//$reportcontent =   $engine->render($data) ;
//
//dump ($_POST);

if ($_POST['operation'] == "pdf") {
	$mpdf = new \Mpdf\Mpdf(['tempDir' => __DIR__ . '/tmp']);

	$mpdf->WriteHTML($reportcontent);
	$mpdf->setFooter('Página {PAGENO} de {nbpg}');
	$mpdf->Output($template['nombre'] . '.pdf', 'D');
} else {
	include('templates/report_viewer.php');
}
//include ('templates/main_tailwind.php');
