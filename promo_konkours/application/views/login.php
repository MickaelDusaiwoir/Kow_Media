<?= form_open('admin/connect', array('id' => 'login'))?>
	<label for="username">Nom d'utilisateur</label>
	<input type="text" name="username" id="username" placeholder="Ex: Rambo" value="<?php echo set_value('username'); ?>" />
	<?= form_error('username'); ?>
	<label for="password">Mot de passe</label>
	<input type="password" name="password" id="password" />
	<?= form_error('password'); ?>
	<input type="submit" value="Se connecter" />
<?= form_close(); ?>
