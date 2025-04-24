

<h1><?= $this->singular_name ?></h1>


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


<?php
	if ($custom_content != ""){
?>
<div class="form" id="custom"> <?php echo $custom_content ;  ?>  </div>
<?php	
	} // fin custom html
  ?>
