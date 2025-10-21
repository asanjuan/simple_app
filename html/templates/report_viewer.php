

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="templates/css/style.css?ver=<?php echo time(); ?>">	
</head>
<body style="background-color:#eeeeee;padding:20px;">
<h1>Visor de informes</h1>

<form id="form_edit" action="" method="POST">

<div class="toolbar">
<!--div class="button-container"-->
    <!--div class="left-buttons"-->
	<input type="hidden" name="operation" id="operation" value="pdf">
        <span type = "button" class="boton-enlace" onclick="window.location.reload();"><img src="templates/img/refresh.svg" class="form-icon" />Actualizar</span>
		<button type = "submit" class="boton-enlace" > PDF</button>
		<span onclick="imprimirIframe()">Imprimir</span>

<script>
function imprimirIframe() {
    var iframe = document.getElementById("rpt_frame");

    // Verifica que el iframe esté cargado
    if (iframe && iframe.contentWindow) {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
    } else {
        alert("El iframe no está disponible.");
    }
}
</script>
</div>


</form>

<iframe id="rpt_frame"
  style="width:100%;height:80%;overflow:auto"
  srcdoc='<?php echo $reportcontent ;  ?>'
></iframe>

</body>
