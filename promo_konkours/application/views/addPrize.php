<?= form_open('admin/addPrize', array('id' => 'addPrize', 'enctype' => 'multipart/form-data', 'class' => "form-horizontal span5")); ?>

	<div>
		<h2>
			Ajouter un cadeau
		</h2>
	</div>

	<?= form_fieldset(); ?>

		<div class="control-group">
			<?= form_label('Titre du cadeau','title', array( 'class' => "control-label")); ?>
			<div class="controls">
				<input type="text" name="title" id="title" class="span12" value="<?php echo set_value('title'); ?>" />
				<?= form_error('title'); ?>
			</div>			
		</div>

		<div class="control-group">
			<?= form_label('Image du cadeau', 'image', array( 'class' => "control-label")); ?>
			<div class="controls">
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
					<input type="text" name="value" class="span11" id="value" value="<?php echo set_value('value'); ?>" />
					<span class="add-on">&euro;</span>
				</div>
				<?= form_error('value'); ?>

				<input type="hidden" name="contest_id" value="<?php echo $id; ?>" />

				<input type="submit" name="envoyer" value="Ajouter ce cadeau" class="btn btn-primary span12" />
			</div>
		</div>

	<?= form_fieldset_close(); ?>

<?= form_close(); ?>