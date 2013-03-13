<?= form_open('admin/connect', array('id' => 'login', 'class' => 'form-horizontal span4'))?>
	<div class="control-group">
		<label for="username" class="control-label" >Nom d'utilisateur</label>
		<div class="controls">
			<input type="text" name="username" id="username" class="span12" placeholder="Ex: Rambo" value="<?php echo set_value('username'); ?>" />
		</div>
		<?= form_error('username'); ?>
	</div>
	<div class="control-group">
		<label for="password" class="control-label">Mot de passe</label>
		<div class="controls">
			<input type="password" name="password" id="password" class="span12" />
		</div>
		<?= form_error('password'); ?>
		<div class="controls">
			<input type="submit" value="Se connecter" class="btn btn-primary span12" />
		</div>
	</div>	
<?= form_close(); ?>
