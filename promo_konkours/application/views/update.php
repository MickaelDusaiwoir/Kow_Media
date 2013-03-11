<?php
	if ( $type == 'contest' ) :
?>

<?= form_open('admin/update', array('id' => 'updateContest', 'class' => "form-horizontal span5")); ?>
	<?= form_fieldset(); ?>

		<div class="control-group">
			<?= form_label('Titre du concours','title', array( 'class' => "control-label") ); ?>
			<div class="controls">
				<input type='text' id="title" name="title" placeholder="Jeu concours organis&eacute; par Banana"  class="span10"
				<?php 	if ( set_value('title') ) 
							echo 'value="'.set_value('title').'"'; 
						else 
							echo 'value="'.$data->title.'"'; ?> />
			</div>
			<?= form_error('title'); ?>
		</div>

		<div class="control-group">
			<?= form_label('Lien du concours','url', array( 'class' => "control-label") ); ?>
			<div class="controls">
				<input type='text' id="url" name="url" placeholder="http://konkours.be" class="span10"
				<?php  	if ( set_value('url') ) 
					echo 'value="'.set_value('url').'"'; 
				else 
					echo 'value="'.$data->url.'"'; ?> />
			</div>
			<?= form_error('url'); ?>
		</div>

		<div class="control-group">
			<?= form_label('Astuces pour ce concours', 'text', array( 'class' => "control-label") ); ?>
			<div class="controls">
				<textarea id="text" name="text" class="span10"><?php  	if ( set_value('text') ) echo set_value('text'); else echo $data->text; ?></textarea>
			
			<?= form_error('text'); ?>

			<input type="hidden" name="id" value="<?php echo $data->id; ?>" />
			<input type="hidden" name="type" value="<?php echo $type ?>" />

			<input type="submit" name="envoyer" value="Modifier ce concours" class="btn btn-primary span10" />

			</div>
		</div>

	<?= form_fieldset_close(); ?>
<?= form_close(); ?>

<?php
	elseif ($type == 'prize') :
?>
	<?= form_open('admin/update', array('id' => 'updatePrize', 'enctype' => 'multipart/form-data', 'class' => "form-horizontal span5")); ?>

		<?= form_fieldset(); ?>

			<div class="control-group">
				<?= form_label('Titre','title',  array( 'class' => "control-label")); ?>
				<div class="controls">
					<input type="text" name="title" id="title" class="span12" value="<?php if ( set_value('title') ) 
																			echo set_value('title'); 
																		else 
																			echo $data->title; ?>" />
				</div>
				<?= form_error('title'); ?>
			</div>

			<div class="control-group">
				<?= form_label('Image', 'image', array( 'class' => "control-label")); ?>
				<div class="controls">
					<img src="<?php echo(base_url() . THUMB_IMG . $data->id); ?>.jpg" class="thumbnail" title="<?php echo $data->title ?>" style="max-width: 128px;max-height:128px;"/>
					<input type="file" name="image" id="image" class="span12" />
				</div>
			</div>

			<div class="control-group">
				<?= form_label('Valeur', 'value', array( 'class' => "control-label") ); ?>
				<div class="controls">
					<div class="input-append row-fluid ">
						<input type="text" name="value" id="value" class="span11" value="<?php if ( set_value('value') ) 
																					echo set_value('value'); 
																				else 
																					echo $data->value; ?>" />
						<span class="add-on">&euro;</span>
					</div>
					<?= form_error('value'); ?>

					<input type="hidden" name="id" value="<?php echo $data->id; ?>" />
					<input type="hidden" name="type" value="<?php echo $type ?>" />

					<input type="submit" name="envoyer" value="Modifier ce cadeau" class="btn btn-primary span12" />
				</div>
			</div>

		<?= form_fieldset_close(); ?>

	<?= form_close(); ?>
<?php
	endif;
?>