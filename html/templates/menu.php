<ul> 
	<?php
	foreach ($menu_elements as $areas){ 
	 ?>
		<li class="has-submenu <?php if (isset($areas['expand']) && $areas['expand']==true) { echo 'active'; }?> ">
		<?php if (isset($areas['items']) && count($areas['items'])>0) { ?>
			<a class="menu-area" href="#"> <?= $areas['option'] ?> </a> 
			<ul class="sub-menu "  >
			<?php
			foreach ($areas['items'] as $opt){ 
			 ?>
				<li class="<?php if ($opt['current']==true) echo "current_menu"; ?>" > <a href="<?= $opt['url'] ?>"> <i class="fas <?php if ($opt['icon']!="") echo $opt['icon']; else echo "fa-table-cells-large"; ?>"></i> <?= $opt['option'] ?> </a></li>
			<?php 
			}
			?>
			</ul>
		<?php 
		} else { 
		?>
			<a href="<?= $areas['url'] ?>"> <?=$areas['option'] ?> </a>
		<?php 
		}
		?>
		</li>
	<?php 
	}
	?>
</ul>