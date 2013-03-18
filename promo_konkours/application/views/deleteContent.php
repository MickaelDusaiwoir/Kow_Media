<div id="delete_content">
	<?php 
		if ( $type == 'contest') : ?>
	<p class="icon-alert">
		Êtes-vous sûr de vouloir archiver ce concours&nbsp;?
	</p>
	<?php 
		else : 
	?>
	<p class="icon-alert">
		Êtes-vous sûr de vouloir archiver ce cadeau&nbsp;?
	</p>
	<?php
		endif;
	?>
	<?=  anchor('admin/delete/'.$id.'/'.$type, 'Oui', array('title' => 'Supprimer ce '.$type, 'class' => 'btn_yes'));  ?>
	<?=  anchor('admin/afficher', 'Non', array('title' => 'Retour à l\'accueil', 'class' => 'btn_no'));  ?>
</div>