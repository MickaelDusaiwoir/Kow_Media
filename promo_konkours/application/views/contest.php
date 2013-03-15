<div id="add_contest_content">
	<?php if ( isset($type) ) : ?>

		<h2>
			Modifier ce concours
		</h2>

		<?= form_open('admin/update', array('id' => 'addContest')); ?>

	<?php else : ?>

		<h2>
			Ajouter un nouveau concours
		</h2>

		<?= form_open('admin/addContest', array('id' => 'addContest')); ?>

	<?php endif; ?>

		<?= form_fieldset(); ?>

			<?= form_label('Titre du concours','title'); ?>
			<input type='text' id="title" name="title" placeholder="Jeu concours organis&eacute; par Banana" 
			<?php 
				if ( set_value('title') ) 
					echo 'value="'.set_value('title').'"'; 
				elseif ( isset($data->title) ) 
					echo 'value="'.$data->title.'"'; 
			?> />
			<?= form_error('title'); ?>


			<?= form_label('Lien du concours','url'); ?>
			<input type='text' id="url" name="url" placeholder="http://konkours.be" 
			<?php 
				if ( set_value('url') ) 
					echo 'value="'.set_value('url').'"'; 
				elseif ( isset($data->url) )
					echo 'value="'.$data->url.'"';
			?> />
			<?= form_error('url'); ?>


			<?= form_label('Astuces', 'text'); ?>
			<textarea id="text" name="text"><?php 
				if( set_value('text') ) 
					echo set_value('text');
				elseif ( isset($data->text) )
					echo $data->text;
			 ?>
			</textarea>
			<?= form_error('text'); ?>


			<?php if ( isset($type) ) : ?>

				<input type="hidden" name="id" 
				<?php 
					if ( isset($data->id) )
						echo 'value="'.$data->id.'"';
					else 
						echo 'value="'.$id.'"'; 
				?> />

				<input type="hidden" name="type" value="<?php echo $type ?>" />
				<input type="submit" name="envoyer" value="Modifier ce concours" />
			
			<?php else : ?>

				<input type="submit" name="envoyer" value="Ajouter ce concours" />

			<?php endif; ?>

		<?= form_fieldset_close(); ?>
	<?= form_close(); ?>
</div>

<div id="legend">
	<h2>
		l&eacute;gende de remplacement pour les url 
	</h2>
	<ul>
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
			Email&nbsp;: %email
			<p>
				Ex: &(e)mail=%email
			</p>
		</li>
		<li>
			Ville&nbsp;: %ville
			<p>
				Ex: &ville=%ville
			</p>
		</li>
		<li>
			Adresse&nbsp;: %adresse
			<p>
				Ex: adresse=%adresse
			</p>
		</li>
		<li>
			Jour de naissance&nbsp;: %jour
			<p>
				Ex: &day_birthdate=%jour
			</p>
		</li>
		<li>
			Mois de naissance&nbsp;: %mois
			<p>
				Ex: &month_birthdate=%mois
			</p>
		</li>
		<li>
			Ann&eacute;e de naissance&nbsp;: %annee
			<p>
				Ex: &year_birthdate=%annee
			</p>
		</li>

		<li>
			Civilit&eacute;&nbsp;: %sexe( civilit&eacute; 1|civilit&eacute; 2 <sup>[|civilit&eacute; 3]* Optionnel</sup>) 
			<p>Ex: &civilite=%sexe(H|F)</p>
			<p>Elle doit toujours être placer en fin d'url</p>
		</li>
		<li>
			Code postal&nbsp;: %code_postal
			<p>
				Ex: code_postal ou cp =%code_postal
			</p>
		</li>
		
	</ul>
</div>