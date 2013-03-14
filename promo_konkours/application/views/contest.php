<div class="span6">
	<?php if ( isset($type) ) : ?>

		<h2 class="span12">
			Modifier ce concours
		</h2>

		<?= form_open('admin/update', array('id' => 'addContest', 'class' => 'form-horizontal span12')); ?>

	<?php else : ?>

		<h2 class="span12">
			Ajouter un nouveau concours
		</h2>

		<?= form_open('admin/addContest', array('id' => 'addContest', 'class' => 'form-horizontal span12')); ?>

	<?php endif; ?>

		<?= form_fieldset(); ?>

		<div class="control-group">
			<?= form_label('Titre du concours','title',  array( 'class' => "control-label")); ?>
			<div class="controls">
				<input type='text' id="title" name="title" class="span12" placeholder="Jeu concours organis&eacute; par Banana" 
				<?php 
					if ( set_value('title') ) 
						echo 'value="'.set_value('title').'"'; 
					elseif ( isset($data->title) ) 
						echo 'value="'.$data->title.'"'; 
				?> />

				<?= form_error('title'); ?>
			</div>
		</div>

		<div class="control-group">
			<?= form_label('Lien du concours','url', array( 'class' => "control-label")); ?>
			<div class="controls">
				<input type='text' id="url" name="url" class="span12" placeholder="http://konkours.be" 
				<?php 
					if ( set_value('url') ) 
						echo 'value="'.set_value('url').'"'; 
					elseif ( isset($data->url) )
						echo 'value="'.$data->url.'"';
				?> />

				<?= form_error('url'); ?>
			</div>
		</div>

		<div class="control-group">
			<?= form_label('Astuces', 'text', array( 'class' => "control-label")); ?>
			<div class="controls">
				<textarea id="text" name="text" class="span12" ><?php 
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
					<input type="submit" name="envoyer" value="Modifier ce concours" class="btn btn-primary span12" />
				
				<?php else : ?>

					<input type="submit" name="envoyer" value="Ajouter ce concours" class="btn btn-primary span12" />

				<?php endif; ?>

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
				Ex: &day_birthdate=%jour
			</p>
		</li>
		<li class="span6">
			Mois de naissance&nbsp;: %mois
			<p>
				Ex: &month_birthdate=%mois
			</p>
		</li>
		<li class="span6">
			Ann&eacute;e de naissance&nbsp;: %annee
			<p>
				Ex: &year_birthdate=%annee
			</p>
		</li>

		<li class="span6">
			Civilit&eacute;&nbsp;: %sexe( civilit&eacute; 1|civilit&eacute; 2 <sup>[|civilit&eacute; 3]* Optionnel</sup>) 
			<p>Ex: &civilite=%sexe(H|F)</p>
			<p>Elle doit toujours être placer en fin d'url</p>
		</li>
		<li class="span6">
			Code postal&nbsp;: %code_postal
			<p>
				Ex: code_postal ou cp =%code_postal
			</p>
		</li>
		
	</ul>
</div>