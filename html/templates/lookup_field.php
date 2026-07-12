<div class="lookupFieldContainer">
	<input class="lookupid" type="hidden" id="<?= $campo ?>" name="<?= $campo ?>" 
		value="<?= $valor_actual ?>"  
		data-field="" 
		data-value="" 
		data-table="<?= $tabla ?>"
		readonly /> 
	
	<input type="text" class="lookupdesc" id="lookupdesc_<?= $campo ?>" name="lookupdesc_<?= $campo ?>" 
		onchange="javascript:lookup_change('<?= $campo ?>', 'lookupdesc_<?= $campo ?>')" 
		value="<?= $descripcion_actual ?>" <?= $required ?> <?= $disabled ?> /> 
	<?php 
	if ($disabled == "") {
	?>
		<div  class="lupa" 
		onclick="javascript:openLookup('<?= $campo ?>', 'lookupdesc_<?= $campo ?>',1)">
		<img src="templates/img/lupa.svg"/>
		</div>
	<?php
	}
	?>
		<?php  if ($url_item != "" ) { ?> <a class="lupa"  href="<?php  echo $url_item; ?>"> <img src="templates/img/lapiz-blog.svg"/> </a> <?php } ?>
</div>