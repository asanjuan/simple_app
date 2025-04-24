<div class="lookupFieldContainer">
	<input class="lookupid" type="hidden" id="<?= $campo ?>" name="<?= $campo ?>" 
		value="<?= $valor_actual ?>" readonly /> 
	
	<input type="text" class="lookupdesc" id="lookupdesc_<?= $campo ?>" name="lookupdesc_<?= $campo ?>" 
		onchange="javascript:lookup_change('<?= $tabla ?>', '<?= $campo_codigo ?>', '<?= $campo_descripcion ?>','<?= $campo ?>', 'lookupdesc_<?= $campo ?>')" 
		value="<?= $descripcion_actual ?>" /> 
	<div  class="lupa" 
		onclick="javascript:openLookup('<?= $tabla ?>', '<?= $campo_codigo ?>', '<?= $campo_descripcion ?>','<?= $campo ?>', 'lookupdesc_<?= $campo ?>')">
		<img src="templates/img/lupa.svg"/>
		</div>
		<?php  if ($url_item != "") { ?> <a class="lupa"  href="<?php  echo $url_item; ?>"> <img src="templates/img/lapiz-blog.svg"/> </a> <?php } ?>
</div>