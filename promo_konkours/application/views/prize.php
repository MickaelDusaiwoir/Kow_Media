<div id="edit_gift">
	<?php if ( isset($type) ) : // on regarde quel type de formulaire on doit utiliser (modifier / ajouter) ?>

		<h2>
			Modifier ce  cadeau
		</h2>

		<?= form_open('admin/update', array('id' => 'addPrize', 'enctype' => 'multipart/form-data')); ?>

			<?= form_fieldset(); ?>

	<?php else : ?>

		<h2>
			Ajouter un cadeau
		</h2>

		<?= form_open('admin/addPrize', array('id' => 'addPrize', 'enctype' => 'multipart/form-data')); ?>

			<?= form_fieldset(); ?>
		
	<?php endif; ?>

				<?= form_label('Titre du cadeau','title'); ?>
				<input type="text" name="title" id="title"  
				<?php 
					if ( set_value('title') ) // S'il y a une valeur saisie, elle est retournée en cas d'erreur.
						echo 'value="'.set_value('title').'"'; 
					elseif ( isset( $data->title) ) // On teste data pour voir s'il y a une valeur (ne fonctionne que si on modifier).
						echo 'value="'.$data->title.'"';
				?> />
				<?= form_error('title'); ?>


				<?= form_label('Image du cadeau', 'image'); ?>
				<input type="file" name="image" id="image" />
				<?php 
					if ( isset($data->id) ) : 
						for ( $i = 0; $i < $data->id; $i++ )
						{
							$n = $i * 100;
							$u = ($i + 1) * 100;

							if ( $n < $data->id && $data->id < $u )
							{
								$folderName = $n.'-'.$u.'/';
								break;
							}
						}
				?>
					<figure>
						<img src="<?= base_url() . THUMB_IMG . $folderName . $data->id; ?>.jpg" title="<?php echo $data->title ?>" />
					</figure>
				<?php endif; ?>
				
				<?php 
					if ( isset($erreur) && $erreur !== null )
						echo '<p class="alert alert-error">'.$erreur.'</p>';
				?>


				<?= form_label('Valeur du cadeau', 'value'); ?>
				<input type="text" name="value" id="value" maxlength="12"
				<?php 
					if ( set_value('value') ) 
						echo 'value="'.set_value('value').'"'; 
					elseif ( isset($data->value) )
						echo 'value="'.$data->value.'"';
				?> />
				<span class="euro_btn">&euro;</span>
				<?= form_error('value'); ?>


				<?php if ( isset($type) ) : ?>
					<div class="clear">
						<?= form_label('Position du cadeau', 'position'); ?>
						<input type="number" placeholder="0" name="position" id="position" min="0" />
					</div>

					<input type="hidden" name="id" 
					<?php 
						if ( isset($data->id) )
							echo 'value="'.$data->id.'"';
						else 
							echo 'value="'.$id.'"'; 

					?> />
					<input type="hidden" name="type" value="<?php echo $type ?>" />
					<input type="submit" name="envoyer" value="Modifier ce cadeau" />

				<?php else : ?>

					<input type="hidden" name="contest_id" value="<?php echo $id; ?>" />
					<input type="submit" name="envoyer" value="Ajouter ce cadeau" />

				<?php endif; ?>

		<?= form_fieldset_close(); ?>

	<?= form_close(); ?>
</div>