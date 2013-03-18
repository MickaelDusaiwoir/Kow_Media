<div id="add_contest_content">
	<?php if ( isset($type) ) : // on regarde quel type de formulaire on doit utiliser (modifier / ajouter). ?>

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
				if ( set_value('title') ) // S'il y a une valeur saisie, elle est retournée en cas d'erreur.
					echo 'value="'.set_value('title').'"'; 
				elseif ( isset($data->title) ) // On teste data pour voir s'il y a une valeur (ne fonctionne que si on modifier).
					echo 'value="'.$data->title.'"'; 
			?> />
			<?= form_error('title'); // Affiche un message d'erreur expliquant cette dernière. ?>


			<?= form_label('Lien du concours','url'); ?>
			<input type='text' id="url" name="url" placeholder="http://konkours.be" 
			<?php 
				if ( set_value('url') ) // S'il y a une valeur saisie, elle est retournée en cas d'erreur.
					echo 'value="'.set_value('url').'"'; 
				elseif ( isset($data->url) ) // On teste data pour voir s'il y a une valeur (ne fonctionne que si on modifier).
					echo 'value="'.$data->url.'"';
			?> />
			<?= form_error('url'); // Affiche un message d'erreur expliquant cette dernière. ?>


			<?php echo form_label('Astuces', 'text'); 

				if ( set_value('text') ) // Trois textarea different pour éviter de se retrouver avec des espaces dans le cas où il n'y a pas de données.
					echo '<textarea id="text" name="text" rows="4" cols="36">'.set_value("text").'</textarea>';
				elseif ( isset($data->text) )
					echo '<textarea id="text" name="text" rows="4" cols="36">'.$data->text.'</textarea>';
				else
					echo '<textarea id="text" name="text" rows="4" cols="36" placeholder="Compléter le formulaire  Accepter le règlement  Gagner votre voyage !! "></textarea>';
			?>
			<?= form_error('text'); // Affiche un message d'erreur expliquant cette dernière. ?>


			<?php 

				// On vérifie que l'on a bien une action à faire (modifier / ajouter).
				// On vérifie que data->id n'est pas vide, sinon on met l'id renvoyer par la fonction.
				// On affiche en consequence le bouton avec le bon nom.

				if ( isset($type) ) : ?>

				<?= form_label('Position du concours', 'position'); ?>
				<input type="number" placeholder="0" name="position" id="position" min="0"/>

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
			<u>Nom&nbsp;:</u> <strong>%nom</strong>
			<small>
				 <em>Ex:</em> &nom=%nom
			</small>
		</li>
		<li>
			<u>Pr&eacute;nom&nbsp;:</u> <strong>%prenom</strong>
			<small>
				<em>Ex:</em> &prenom=%prenom
			</small>
		</li>
		<li>
			<u>Email&nbsp;:</u> <strong>%email</strong>
			<small>
				<em>Ex:</em> &(e)mail=%email
			</small>
		</li>
		<li>
			<u>Ville&nbsp;:</u> <strong>%ville</strong>
			<small>
				<em>Ex:</em> &ville=%ville
			</small>
		</li>
		<li>
			<u>Adresse&nbsp;:</u> <strong>%adresse</strong>
			<small>
				<em>Ex:</em> &adresse=%adresse
			</small>
		</li>
		<li>
			<u>Jour de naissance&nbsp;:</u> <strong>%jour</strong>
			<small>
				<em>Ex:</em> &day_birthdate=%jour
			</small>
		</li>
		<li>
			<u>Mois de naissance&nbsp;:</u> <strong>%mois</strong>
			<small>
				<em>Ex:</em> &month_birthdate=%mois
			</small>
		</li>
		<li>
			<u>Ann&eacute;e de naissance&nbsp;:</u> <strong>%annee</strong>
			<small>
				<em>Ex:</em> &year_birthdate=%annee
			</small>
		</li>
		<li>
			<u>Code postal&nbsp;:</u> <strong>%code_postal</strong>
			<small>
				<em>Ex:</em> &code_postal ou cp =%code_postal
			</small>
		</li>
		<li>
			<u>Civilit&eacute;&nbsp;:</u> <strong>%sexe( civilit&eacute; 1|civilit&eacute; 2 <sup>[|civilit&eacute; 3]* Optionnel</sup>)</strong> 
			<small>
				<em>Ex:</em> &civilite=%sexe(H|F)
			</small>
			<p class="icon-alert">
				Elle doit toujours être placer en fin d'url
			</p>
		</li>		
	</ul>
</div>