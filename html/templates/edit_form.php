



<form id="form_edit" action="<?php  echo $url_controller_item; ?>" method="POST" enctype="multipart/form-data">

<div class="flex-columns">

	<div class="toolbar">

		<input type="hidden" name="operation" id="operation" value="">
			<!--a href="back_history.php" class="boton-enlace"><i class="fas fa-arrow-left"></i>  <span> Atr&aacute;s </span></a-->
			<button type="button" class="boton-enlace" onclick="goBack()"> <i class="fas fa-arrow-left"></i>  <span>  Atr&aacute;s </span></button>
			
			<button type="button" class="boton-enlace" onclick="reloadPage()"> <i class="fa-solid fa-arrows-rotate"></i> <span> Actualizar </span></button>
			
			<?php if ($this->access_update) { ?>
			<button type="submit" class="boton-enlace" name="operation" value="guardar"> <i class="fa-solid fa-floppy-disk"></i> <span>Guardar</span></button> 
			<?php
			}
			?>		
			<?php if ($this->access_insert) { ?>
			<a href="<?php  echo $url_controller_new; ?>" class="boton-enlace" ><i class="fa-solid fa-plus"></i><span> Nuevo</span></a>
			<button type="submit" class="boton-enlace" name="operation" value="duplicate"> <i class="fa-solid fa-copy"></i><span> Duplicar</span> </button> 
			<?php
			}		
			?>
				
			

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
					echo '<button type="button" class="boton-enlace" name="operation" value="' . $button["action"] . '" onclick="submitForm(\'form_edit\',this.value,\''.$button["label"].'\')" > ';
					if ($button["icon"]!="") echo '<i class="fa-solid '.$button["icon"].'"></i>';
					echo "<span> " . $button["label"] ." </span>" ;
					echo '</button>';
				}else if ($button["type"]=="script_form"){ 
					echo '<button type="button" class="boton-enlace" name="' . $button["action"] . '" > '. $button["label"] .'</button>';
				}
			}
			
			//ahora los informes 
			if (count($this->reports)>0){
				$html = '<div class="rpt-dropdown boton-enlace "> <span >Seleccionar Informe ▼</span> <div class="rpt-dropdown-content">';
				foreach ($this->reports as $rpt){
					$html .= '<a href="/reportviewer.php?template_id='.$rpt['id'].'&item='.$item.'" target="_blank">'.$rpt['nombre'].'</a>';
				}
				$html .= '</div></div>';
				echo $html;
			}

			?>
			
		
	</div>	

	

	<div class="flex-rows flex-vertical-center">
		<div><h1><?= $this->singular_name ?></h1></div>
		<?php if (!$this->access_update) { 
		echo "<div>(Solo lectura)</div>";
		}
		?>
		<?php
		foreach ($this->messages as $msg ){
			
			echo '<div class="message flex-expand"> ' . t($msg) . '</div>';
			
		}
 ?>

	</div>

	<?php
	if ($item != '' && count($this->process)>0){
		
		echo print_process($this->process[0]['id'], $item);
	}
	?>

	<div class="form flex-expand scrollable">

	<?php
		echo $campos_html;
	?>
	
	</div>
</div>
</form>

  <?php
	if ($custom_content != ""){
?>
<div class="form" id="custom"> <?php echo $custom_content ;  ?>  </div>
<?php	
	} // fin custom html
  ?>
