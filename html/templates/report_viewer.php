

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
        <button class="boton-enlace" onclick="location.reload();"><img src="templates/img/refresh.svg" class="form-icon" />Actualizar</button>
		<button type = "submit" class="boton-enlace" > PDF</button>

</div>


</form>

<iframe
  style="width:100%;height:80%;overflow:auto"
  srcdoc='<?php echo $reportcontent ;  ?>'
></iframe>

</body>
