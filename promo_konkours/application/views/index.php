<div id="userData" class="span3">
	<h2>
		Informations personnelles
	</h2>
	<form method="post" action="#">

		<label for="civilite">Civilité</label>
		<select id="civilite" name="civilite" class="span12">
			<option value="0">Vous êtes :</option>
			<option value="man">Homme</option>
			<option value="woman">Femme</option>
			<option value="miss">Mademoiselle</option>
		</select> 

		<label for="nom">Nom</label>
		<input id="nom" name="nom" type="text" placeholder="Rambo" class="span12" />

		<label for="prenom">Pr&eacute;nom</label>
		<input id="prenom" name="prenom" type="text" placeholder="John" class="span12" />

		<label>Date de naissance</label>
		<select id="jour" name="jour" class="span3"></select>
		<select id="mois" name="mois" class="span5"></select>
		<select id="annee" name="annee" class="span4"></select>

		<label for="email">E-mail</label>
		<input id="email" name="email" type="email" placeholder="john.rambo@exemple.com" class="span12">

		<label for="add">Adresse</label>
		<input id="adresse" name="adresse" type="text" placeholder="Rue du champ de mine n°1" class="span12"/>

		<div class="row-fluid">
			<label for="cp" class="span4">Code postal</label>
			<label for="ville" class="span7">Ville</label>

			<input id="code_postal" name="code_postal" type="text" placeholder="4000"class="span4" />
			<input id="ville" name="ville" type="text" placeholder="Li&egrave;ge" class="span8" />
		</div>
		
		<input type="submit" value="Envoyer" name="envoyer" class="btn btn-primary span12" />
	</form>
</div>

<div class="contests span9">
<h2>
	Tous nos concours !!
</h2>

<?php 
	
	$display = '';

	foreach ( $contests_with_prizes as $contest_with_prize ) : 

		$display .= '<article class="contest">';
		$display .= '<div class="row-fluid">';
		$display .= '<h3>'.$contest_with_prize['title'].'</h3>';

		foreach ( $contest_with_prize['prizes_data'] as $prize ) 
		{	
			$display .= '<div class="span3 prize">';
			$display .= '<h4>'.$prize['title'].'</h4>';
			$display .= '<img src="'.base_url() . THUMB_IMG . $prize['id'] .'.jpg" class="img-polaroid" title="'.$prize['title'].'" style="max-width: 128px;max-height:128px;"/>';
			$display .= '<p> Valeur du cadeau&nbsp;: <em>'.$prize['value'].'&euro;</em></p>';

			if ( $this->session->userdata('Connected') ) 
			{
				$display .= '<div class="action actionPrize">';
				$display .=  anchor('admin/updateView/'.$prize['id'].'/prize', '<span>Modifier ce cadeau</span>', array('title' => 'Modifier ce cadeau', 'class' => 'icon-edit')); 
				$display .=  anchor('admin/deleteView/'.$prize['id'].'/prize', '<span>Supprimer ce cadeau</span>', array('title' => 'Supprimer ce cadeau', 'class' => 'icon-cancel')); 
				$display .= '</div>';
			}
			$display .= '</div>';
		}

		$display .= '<h4 class="astuces">Astuces</h4>';
		$display .= '<p>'.str_replace("\n", '<br />',$contest_with_prize['text']).'</p>';

		$display .= anchor($contest_with_prize['url'], 'Cliquez ICI pour jouer à ce concours', array('title' => 'Cliquez ICI pour jouer à ce concours', 'class' => 'btn btn-primary'));

		if ( $this->session->userdata('Connected') ) 
		{
			$display .= '<div class="action actionContest">';
			$display .= anchor('admin/addPrizesView/'.$contest_with_prize['id'], '<span>Ajouter un cadeau</span>', array('title' => 'Ajouter un cadeau', 'class' => 'icon-plus')); 
			$display .= anchor('admin/updateView/'.$contest_with_prize['id'].'/contest', '<span>Modifier ce concours</span>', array('title' => 'Modifier ce concours', 'class' => 'icon-edit')); 
			$display .= anchor('admin/deleteView/'.$contest_with_prize['id'].'/contest', '<span>Supprimer ce concours</span>', array('title' => 'Supprimer ce concours', 'class' => 'icon-cancel')); 
			$display .= '</div>';
		}

		$display .= '</div>';
		$display .= '</article>';

	endforeach;

	echo $display; 
?>

</div>