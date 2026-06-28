

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="templates/css/style.css?ver=<?php echo time(); ?>">	
    <script>

    let scale = 1;
    function imprimirIframe() {
        var iframe = document.getElementById("rpt_frame");

        // Verifica que el iframe esté cargado
        if (iframe && iframe.contentWindow) {
            scale = 1; // Reinicia el zoom antes de imprimir
            sendZoom(); // Asegura que el zoom se aplique antes de imprimir
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } else {
            alert("El iframe no está disponible.");
        }
    }


    function zoomIn() {
        scale += 0.1;
        sendZoom();
    }

    function zoomOut() {
        scale -= 0.1;
        sendZoom();
    }

    async function sendZoom() {
        const iframe = document.getElementById('rpt_frame');
        document.getElementById('zoom-level').innerText = Math.round(scale * 100) + '%';
        await iframe.contentWindow.postMessage({
            type: "zoom",
            value: scale
        }, "*");
    }
    </script>
</head>
<body style="padding:10px;">

<div class="flex-columns">


<div class="toolbar">
<form id="form_edit" action="" method="POST">
<!--div class="button-container"-->
    <!--div class="left-buttons"-->
	<input type="hidden" name="operation" id="operation" value="pdf">
        <span class="boton-enlace" type = "button" class="boton-enlace" onclick="window.location.reload();"><img src="templates/img/refresh.svg" class="form-icon" />Actualizar</span>
		<button type = "submit" class="boton-enlace" > Exportar </button>
		<span class="boton-enlace" onclick="imprimirIframe()">Imprimir</span>
        <span  class="boton-enlace"> 
            <button type="button" onclick="zoomIn()">+</button>
            <button type="button"  onclick="zoomOut()">-</button> <span id="zoom-level">100%</span>
        </span>
</form>
</div>
<div class="flex-expand">
<iframe id="rpt_frame"
  style="transform-origin: top left;width:100%;height:100%;"
  srcdoc='<?php echo $reportcontent ;  ?>'
></iframe>
</div>
</div>
</body>
