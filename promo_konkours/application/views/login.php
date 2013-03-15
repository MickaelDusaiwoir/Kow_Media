<?php 
	if ( !$this->session->userdata('Connected') ) : 
?>
	<div id="login_page">
		<h2>
			Connection
		</h2>
		<?= form_open('admin/connect', array('id' => 'login'))?>
				
				<label for="username" >Nom d'utilisateur</label>
				<input type="text" name="username" id="username" placeholder="anonymous" value="<?php echo set_value('username'); ?>" />
				<?= form_error('username'); ?>


				<label for="password">Mot de passe</label>
				<input type="password" name="password" id="password" placeholder="***********" />
				<?= form_error('password'); ?>

				<input type="submit" value="Se connecter" />

		<?= form_close(); ?>
	</div>
<?php 
	else :
		redirect('admin/afficher');
	endif; 
?>