<!DOCTYPE html>
<html>
<head>
	<title><?php echo print_title_name(); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="templates/css/colors.php" />	
	<link rel="stylesheet" href="templates/css/style.css" />	
	<script  src="templates/js/myapplib.js"></script>
	<!-- Incluye Chart.js desde el CDN -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
	<script src="tinymce/js/tinymce/tinymce.min.js"></script>
	<script src="templates/js/lookup.js?ver=<?php echo time(); ?>"></script>
	<script src="templates/js/modal.js?ver=<?php echo time(); ?>"></script>
	<script src="templates/js/subgrids.js?ver=<?php echo time(); ?>"></script>
	<script src="templates/js/process_client.js?ver=<?php echo time(); ?>"></script>
	<script src="templates/js/graphics.js?ver=<?php echo time(); ?>"></script>
	<script src="templates/js/table_sort.js?ver=<?php echo time(); ?>"></script>
	<script src="templates/js/editors_config.js"></script>
	<script src="templates/js/menu.js?ver=<?php echo time(); ?>"></script>
	<script src="templates/js/navigation.js?ver=<?php echo time(); ?>"></script>

<?php  add_form_scripts(); ?>	

</head>

<body >
<div id="page">
	<div class="header flex-rows flex-vertical-center">
		<div class="app-logo" ><?php include 'templates/img/app_logo.svg'; ?></div>
		<div class="app-title"><?php echo print_app_name(); ?></div>
		<div class="flex-expand"></div>
		<div ><?php show_user(); ?></div>
	</div>

	<div class="container">

		<div class="container-cols">

			<div class="sidebar">
			<div class="mobile-menu"> <i class="fa-solid fa-bars"></i> <span> Men&uacute; </span> </div>
				<div class="mobile-menu-list">
					
					<?php echo print_menu(); ?>
				</div>
			</div>

			<div id="main" class="main scrollable">
				<div class="modal" id="modal">
					<div class="modal-content">
						<h1 id="modal-title">T&iacute;tulo</h1>
						<img class="loading-image" src="templates/img/Spinner-3.gif" alt="Cargando...">
					</div>
				</div>
				<div class="modal form" id="modal_form">

					<div class="modal-form-content" id="modal-form-content">
						<form id="modal-form-data" action="" method="post" enctype="multipart/form-data">

						</form>
						<div > 
						<button id="acceptButton"  onclick="javascript:ejecutarFormulario();">Aceptar</button>
						<button id="closeButton"  onclick="javascript:ocultarFormulario();">Cerrar</button> 			 
						</div>
					</div>
				</div>
				<div class="modal" id="modal_lookup">

					<div class="modal-content">
						<div > 
							<button id="closeButton" class="close-button" onclick="javascript:closeLookup();">X</button> 
						</div>
						
						<div style="margin-top:20px;max-height:80%;">
							
							<div id="lookup_grid" class="datagrid-lookup"  
								data-view-id=""
								data-field="" 
								data-value="" 
								>
									<div style="padding:10px;" >Loading...</div>
							</div>
						</div>
					</div>
				</div>


				<?php echo print_main_form(); ?>


				<?php echo print_debug_request(); ?>
			</div>

		</div>
	</div>

	<!--div class="header">
		<span class="app-title"><?php echo print_app_name(); ?></span>
	</div-->
	

	
</div>

</body>

</html>
