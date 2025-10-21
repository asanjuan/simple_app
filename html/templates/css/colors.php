<?php
/*** set the content type header ***/
/*** Without this header, it wont work ***/
header("Content-type: text/css");

require '../../config.php';
require '../../utilities.php';
require '../../database.php';
?>

:root {

	--color-cabecera: <? echo get_config("APP_HEADER_COLOR"); ?>; /*azul elegante Microsoft*/
	
	/* Declaraci�n de variables de la paleta de colores */
	/*--color-cabecera: #00204f  ; /*azul old dynamics*/
	/*--color-cabecera: #00838f  ; /*azul Business Central*/
	/*--color-cabecera:#004481; /* azul BBVA */
	/*--color-cabecera: #0D83D2; /*azul elegante Microsoft*/
	/*--color-cabecera: #0066FF; /*azul profesional*/
	/*--color-cabecera: #6366F1; /*Indigo moderno*/
	/*--color-cabecera: #4338CA; /*Indigo oscuro*/
	/*--color-cabecera: #EF4444; /*red 500 tailwind*/
	/*--color-cabecera: #fb2245; /*torch red*/
	/*--color-cabecera: #8B5CF6; /*purple 500 tailwind*/
	/*--color-cabecera: #742774; /*Morado power apps*/
	/*--color-cabecera: #f16434; /*Naranja*/
	/*--color-cabecera: #c26d0c; /*Naranja apagado*/
	/*--color-cabecera: #a35b08; /*Marron*/
	/*--color-cabecera: #888888; /*Gris*/
	/*--color-cabecera: #444444; /*Casi-Negro*/
	/*--color-cabecera: #0D9488; /*Verde profesional*/
	/*--color-cabecera: #134E4A; /*Verde sobrio*/
	/*--color-cabecera: #78be20; /*Verde lima*/


	--color-cabecera-texto: #ffffff;
	/*--color-cabecera-texto: #333;/* */

	--color-texto: #444444;

	/* color resaltado del menu, negativo de la sidebar*/
	/*--resaltado-negativo: #dddddd;*/
	--resaltado-negativo: #333333;

	/*--color-fondo-primero:  #37506f; */
	/*--color-fondo-primero: #f4f4f4; /*sidebar*/
	--color-fondo-primero: #eeeeee;
	/*sidebar*/

	--color-fondo-segundo: #eeeeee;
	/*toolbar y fila resaltada*/
	/*--color-fondo-tercero: #ffffff; */
	--color-fondo-tercero: #f9f9f9;
	/*formulario*/
	/*formulario*/
	--color-fondo-blanco: #ffffff;
	/* fondo de tablas */
	--bordes-visuales: #dddddd;
	/*bordes de elementos, si es necesario*/
	--mensaje-ok: #c7f0c2;
	--mensaje-ok-border: #22e309;
	--color-cabecera-tabla: #ffffff;
	--color-cabecera-tabla-texto: #333333;
}
