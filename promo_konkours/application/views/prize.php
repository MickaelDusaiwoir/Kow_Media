<div>
	<?php if ( isset($type) ) : ?>

		<?= form_open('admin/update', array('id' => 'addPrize', 'enctype' => 'multipart/form-data', 'class' => "form-horizontal span5")); ?>

			<?= form_fieldset(); ?>

				<h2>
					Modifier ce  cadeau
				</h2>

	<?php else : ?>

		<?= form_open('admin/addPrize', array('id' => 'addPrize', 'enctype' => 'multipart/form-data', 'class' => "form-horizontal span5")); ?>

			<?= form_fieldset(); ?>
		
				<h2>
					Ajouter un cadeau
				</h2>
		
	<?php endif; ?>

		

			<div class="control-group">
				<?= form_label('Titre du cadeau','title', array( 'class' => "control-label")); ?>
				<div class="controls">
					<input type="text" name="title" id="title" class="span12" 
					<?php 
						if ( set_value('title') ) 
							echo 'value="'.set_value('title').'"'; 
						elseif ( isset( $data->title) )
							echo 'value="'.$data->title.'"';
					?> />
					<?= form_error('title'); ?>
				</div>			
			</div>

			<div class="control-group">
				<?= form_label('Image du cadeau', 'image', array( 'class' => "control-label")); ?>
				<div class="controls">
					<?php if ( isset($data->id) ) : ?>
						<img src="<?php echo(base_url() . THUMB_IMG . $data->id); ?>.jpg" class="thumbnail" title="<?php echo $data->title ?>" style="max-width: 128px;max-height:128px;"/>
					<?php endif; ?>

					<input type="file" name="image" id="image" />
					<?php 
					if ( isset($erreur) && $erreur !== null )
						echo '<p class="alert alert-error">'.$erreur.'</p>';
				?>
				</div>
			</div>

			<div class="control-group">
				<?= form_label('Valeur du cadeau', 'value', array( 'class' => "control-label") ); ?>
				<div class="controls">
					<div class="input-append row-fluid ">
						<input type="text" name="value" class="span11" id="value" 
						<?php 
							if ( set_value('value') ) 
								echo 'value="'.set_value('value').'"'; 
							elseif ( isset($data->value) )
								echo 'value="'.$data->value.'"';
						?> />
						<span class="add-on">&euro;</span>
					</div>
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
						<input type="submit" name="envoyer" value="Modifier ce cadeau" class="btn btn-primary span12" />

					<?php else : ?>

						<input type="hidden" name="contest_id" value="<?php echo $id; ?>" />
						<input type="submit" name="envoyer" value="Ajouter ce cadeau" class="btn btn-primary span12" />

					<?php endif; ?>
				</div>
			</div>

		<?= form_fieldset_close(); ?>

	<?= form_close(); ?>
</div>