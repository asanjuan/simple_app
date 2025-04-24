

<h1><?= $this->singular_name ?></h1>

<form id="form_edit" action="<?php  echo $url_controller_item; ?>" method="POST" enctype="multipart/form-data">

<div class="toolbar">
<!--div class="button-container"-->
    <!--div class="left-buttons"-->
	<input type="hidden" name="operation" id="operation" value="">
        <a href="back_history.php" class="boton-enlace"><img src="templates/img/flecha-circulo-izquierda.svg" class="form-icon" /> Atr&aacute;s</a>
		<button class="boton-enlace" onclick="location.reload();"><img src="templates/img/refresh.svg" class="form-icon" />Actualizar</button>
			
		<?php 
		
		foreach($this->custom_links as $link){
			//if ($button["type"]=="form"){ 
			$target = "";
			//var_dump($link);
			if ( $link['new_window'] === true) {
				$target = 'target="_blank"';
			}
			echo '<a href="'.$link['url'].'" class="boton-enlace" '.$target.'><img src="'.$link['img'].'" class="form-icon" /> '.$link['label'].'</a>';
			//}
		}
		
		?>
		<?php 
		foreach($this->custom_buttons as $button){
			if ($button["type"]=="form"){ 
				echo '<button type="button" class="boton-enlace" name="operation" value="' . $button["action"] . '" onclick="submitForm(\'form_edit\',this.value,\''.$button["label"].'\')" > '. $button["label"] .'</button>';
			}
		}
		
		?>
		
	
	
</div>
<?php
foreach ($this->messages as $msg ){
	
	echo '<div class="message"> ' . t($msg) . '</div>';
	
}
 ?>
<div class="form">

  <?php
	echo $campos_html;
  ?>
  
</div>

</form>

<?php
	if ($custom_content != ""){
?>
<div class="form" id="custom"> <?php echo $custom_content ;  ?>  </div>
<?php	
	} // fin custom html
  ?>
