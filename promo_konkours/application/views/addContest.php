<h2>
	Ajouter un nouveau concours
</h2>

<?= form_open('admin/saveContest', array('id' => 'addContest')); ?>
<?= form_fieldset(); ?>
<?= form_label('Titre du concours','title'); ?>
<input type='text' id="title" name="title" placeholder="Jeu concours organis&eacute; par Banana" value="<?php echo set_value('title'); ?>" />
<?= form_error('title'); ?>

<?= form_label('Lien du concours','url'); ?>
<input type='text' id="url" name="url" placeholder="http://konkours.be" value="<?php echo set_value('url'); ?>" />
<?= form_error('url'); ?>

<?= form_label('Astuces (texte preformater)', 'text'); ?>
<textarea id="text" name="text" value="<?php echo set_value('text'); ?>"></textarea>
<?= form_error('text'); ?>

<?= form_fieldset_close(); ?>
<?= form_close(); ?>

<section>
	<h2>
		l&eacute;gende de remplacement pour les url 
	</h2>
	<ul id="legend">
		<li>
			civilit&eacute;&nbsp;: %sexe( civilit&eacute; 1 | civilit&eacute; 2 [ | civilit&eacute; 3]* ) 
			<p>Ex: &civilite=%sexe(H|F)</p>
			<p>Les param&egrave;tre de la civilit&eacute; sont définit diff&eacute;remment selon le site</p>
			<p>* Optionnel</p>
		</li>
		<li>
			Nom&nbsp;: %nom
			<p>
				 Ex: &nom=%nom
			</p>
		</li>
		<li>
			Pr&eacute;nom&nbsp;: %prenom
			<p>
				Ex: &prenom=%prenom
			</p>
		</li>
		<li>
			(e)mail&nbsp;: %email
			<p>
				Ex: &(e)mail=%email
			</p>
		</li>
		<li>
			Jour de naissance&nbsp;: %jour
			<p>
				Ex: &day_birthday=%jour
			</p>
		</li>
		<li>
			Mois de naissance&nbsp;: %mois
			<p>
				Ex: &month_birthday=%mois
			</p>
		</li>
		<li>
			Ann&eacute;e de naissance&nbsp;: %annee
			<p>
				Ex: &year_birthday=%annee
			</p>
		</li>
		<li>
			Adresse&nbsp;: %adresse
			<p>
				Ex: adresse=%adresse
			</p>
		</li>
		<li>
			Code postal&nbsp;: %code_postal
			<p>
				Ex: code_postal ou cp =%code_postal
			</p>
		</li>
		<li>
			Ville&nbsp;: %ville
			<p>
				Ex: &ville=%ville
			</p>
		</li>
	</ul>
</section>