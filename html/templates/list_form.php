
<div class="flex-columns">
	<div>
	<h1><?= $metadata["plural_name"] ?></h1>
	</div>
	<div class="flex-expand scrollable">
		<div class="panel_item_list">
			<div class="panel_item2" style="width: 100%">
				<div id="maingrid" class="datagrid"  
				data-gridurl="<?php echo get_URL_BASE(); ?>/forms/subgrid.php" 
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
