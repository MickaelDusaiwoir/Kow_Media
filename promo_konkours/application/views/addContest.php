<div class="span6">
	<h2 class="span12">
		Ajouter un nouveau concours
	</h2>

	<?= form_open('admin/addContest', array('id' => 'addContest', 'class' => 'form-horizontal span12')); ?>
		<?= form_fieldset(); ?>

		<div class="control-group">
			<?= form_label('Titre du concours','title',  array( 'class' => "control-label")); ?>
			<div class="controls">
				<input type='text' id="title" name="title" class="span12" placeholder="Jeu concours organis&eacute; par Banana" value="<?php echo set_value('title'); ?>" />
				<?= form_error('title'); ?>
			</div>
		</div>

		<div class="control-group">
			<?= form_label('Lien du concours','url', array( 'class' => "control-label")); ?>
			<div class="controls">
				<input type='text' id="url" name="url" class="span12" placeholder="http://konkours.be" value="<?php echo set_value('url'); ?>" />
				<?= form_error('url'); ?>
			</div>
		</div>

		<div class="control-group">
			<?= form_label('Astuces', 'text', array( 'class' => "control-label")); ?>
			<div class="controls">
				<textarea id="text" name="text" class="span12" ><?php echo set_value('text'); ?></textarea>

				<?= form_error('text'); ?>

				<input type="submit" name="envoyer" value="Ajouter ce concours" class="btn btn-primary span12" />
			</div>
		</div>

		<?= form_fieldset_close(); ?>
	<?= form_close(); ?>
</div>

<div class="span6">
	<h2>
		l&eacute;gende de remplacement pour les url 
	</h2>
	<ul id="legend" class="alert alert-info span8">
		<li class="span6">
			Nom&nbsp;: %nom
			<p>
				 Ex: &nom=%nom
			</p>
		</li>
		<li class="span6">
			Pr&eacute;nom&nbsp;: %prenom
			<p>
				Ex: &prenom=%prenom
			</p>
		</li>
		<li class="span6">
			Email&nbsp;: %email
			<p>
				Ex: &(e)mail=%email
			</p>
		</li>
		<li class="span6">
			Ville&nbsp;: %ville
			<p>
				Ex: &ville=%ville
			</p>
		</li>
		<li class="span6">
			Adresse&nbsp;: %adresse
			<p>
				Ex: adresse=%adresse
			</p>
		</li>
		<li class="span6">
			Jour de naissance&nbsp;: %jour
			<p>
				Ex: &day_birthday=%jour
			</p>
		</li>
		<li class="span6">
			Mois de naissance&nbsp;: %mois
			<p>
				Ex: &month_birthday=%mois
			</p>
		</li>
		<li class="span6">
			Ann&eacute;e de naissance&nbsp;: %annee
			<p>
				Ex: &year_birthday=%annee
			</p>
		</li>

		<li class="span6">
			Civilit&eacute;&nbsp;: %sexe( civilit&eacute; 1|civilit&eacute; 2 <sup>[|civilit&eacute; 3]* Optionnel</sup>) 
			<p>Ex: &civilite=%sexe(H|F)</p>
		</li>
		<li class="span6">
			Code postal&nbsp;: %code_postal
			<p>
				Ex: code_postal ou cp =%code_postal
			</p>
		</li>
		
	</ul>
</div>