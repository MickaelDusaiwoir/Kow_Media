<h2>
	Ajouter un cadeau
</h2>

<?= form_open('admin/addPrize', array('id' => 'addPrize', 'enctype' => 'multipart/form-data')); ?>

	<?= form_fieldset(); ?>

		<?= form_label('Titre du cadeau','title'); ?>
		<input type="text" name="title" id="title" value="<?php echo set_value('title'); ?>" />
		<?= form_error('title'); ?>

		<?= form_label('Image du cadeau', 'image'); ?>
		<input type="file" name="image" id="image" />
		<?php 
			if ( isset($erreur) && $erreur == null )
				echo '<p>'.$erreur.'</p>';
		?>

		<?= form_label('Valeur du cadeau', 'value' ); ?>
		<input type="text" name="value" id="value" value="<?php echo set_value('value'); ?>" />
		<?= form_error('value'); ?>

		<input type="hidden" name="contest_id" value="<?php echo $id; ?>" />

		<?= form_submit('envoyer','Ajouter le cadeau'); ?>

	<?= form_fieldset_close(); ?>

<?= form_close(); ?>