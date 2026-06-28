
<div class="flex-columns">
	<div>
	<h1>
		<i class="fas <?php if ($metadata['icon']!="") echo $metadata['icon']; else echo "fa-table-cells-large"; ?>"></i>
		<?= $metadata["plural_name"] ?></h1>
	</div>
	<div class="flex-expand scrollable">
		<div class="flex-columns">
			<div class="flex-expand" style="width: 100%">
				<div id="maingrid" class="datagrid flex-columns"  
				data-gridurl="forms/subgrid.php<?php echo ( get_Focus_mode() ? "?focusmode=true" : "") ?>"
				data-view-id="<?=$primera_vista?>"
				data-field="" data-value="" 
				data-enabled="<?=$grid_enabled?>"
				data-page-size="<?php echo get_config("VIEW_PAGE_SIZE") ; ?>">
					Loading...
				</div>
				</div>
			</div>
			
		</div>
	</div>
</div>	
