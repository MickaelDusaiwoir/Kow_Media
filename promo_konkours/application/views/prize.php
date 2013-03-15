<div>
	<?php if ( isset($type) ) : ?>

		<?= form_open('admin/update', array('id' => 'addPrize', 'enctype' => 'multipart/form-data')); ?>

			<?= form_fieldset(); ?>

				<h2>
					Modifier ce  cadeau
				</h2>

	<?php else : ?>

		<?= form_open('admin/addPrize', array('id' => 'addPrize', 'enctype' => 'multipart/form-data')); ?>

			<?= form_fieldset(); ?>
		
				<h2>
					Ajouter un cadeau
				</h2>
		
	<?php endif; ?>


				<?= form_label('Titre du cadeau','title'); ?>
				<input type="text" name="title" id="title"  
				<?php 
					if ( set_value('title') ) 
						echo 'value="'.set_value('title').'"'; 
					elseif ( isset( $data->title) )
						echo 'value="'.$data->title.'"';
				?> />
				<?= form_error('title'); ?>


				<?= form_label('Image du cadeau', 'image'); ?>
				<?php 
					if ( isset($data->id) ) : ?>
						<img src="<?php echo(base_url() . THUMB_IMG . $data->id); ?>.jpg" title="<?php echo $data->title ?>" />
				<?php endif; ?>

				<input type="file" name="image" id="image" />
				<?php 
					if ( isset($erreur) && $erreur !== null )
						echo '<p class="alert alert-error">'.$erreur.'</p>';
				?>


				<?= form_label('Valeur du cadeau', 'value'); ?>
				<input type="text" name="value" id="value" 
				<?php 
					if ( set_value('value') ) 
						echo 'value="'.set_value('value').'"'; 
					elseif ( isset($data->value) )
						echo 'value="'.$data->value.'"';
				?> />
				<span>&euro;</span>
				<?= form_error('value'); ?>


				<?php if ( isset($type) ) : ?>

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