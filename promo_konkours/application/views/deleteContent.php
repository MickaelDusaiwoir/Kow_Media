<div>
	<?php 
		if ( $type == 'contest') : ?>
	<p>
		Êtes-vous sûr de vouloir archiver ce concours
	</p>
	<?php 
		else : 
	?>
	<p>
		Êtes-vous sûr de vouloir archiver ce cadeau
	</p>
	<?php
		endif;
	?>
	<?=  anchor('admin/delete/'.$id.'/'.$type, 'Oui', array('title' => 'Supprimer ce'.$type));  ?>
	<?=  anchor('admin/afficher', 'Non', array('title' => 'Retour à l\'accueil'));  ?>
</div>