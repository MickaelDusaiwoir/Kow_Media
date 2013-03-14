<div id="userData" class="span3 offset1">
	<h2>
		Informations personnelles
	</h2>
	<form method="post" action="#" class="form-inline" id="form">


		<label for="nom">Nom</label>			
		<input id="nom" name="nom" type="text" placeholder="Rambo" class="span4" />

		<label for="prenom" id="firstname">Pr&eacute;nom</label>
		<input id="prenom" name="prenom" type="text" placeholder="John" class="span4" />

		<label for="man">Monsieur</label>
		<input type="radio" id="man" name="civilite" value="man"/>
		<label for="woman">Madame</label>
		<input type="radio" id="woman" name="civilite" value="woman"/>
		<label for="miss">Mademoiselle</label>
		<input type="radio" id="miss" name="civilite" value="miss"/>

		<label  id="ddn">Date de naissance</label>
		<select id="jour" name="jour" class="span3"></select>
		<select id="mois" name="mois" class="span5"></select>
		<select id="annee" name="annee" class="span4"></select>

		<label for="email" class="span3">E-mail</label>
		<input id="email" name="email" type="email" placeholder="john.rambo@exemple.com" class="span9">

		<label for="add" class="span3">Adresse</label>
		<input id="adresse" name="adresse" type="text" placeholder="Rue du champ de mine n°1" class="span9"/>

		<label for="cp">CP</label>
		<input id="code_postal" name="code_postal" type="text" placeholder="4000"class="span3" />

		<label for="ville" id="city">Ville</label>
		<input id="ville" name="ville" type="text" placeholder="Li&egrave;ge" class="span5" />
	
	</form>
</div>

<div class="contests span6 offset1">
<h2>
	Tous nos concours !!
</h2>

<?php 
	
	$display = '';

	foreach ( $contests_with_prizes as $contest_with_prize ) : 

		$display .= '<article class="contest">';
		$display .= '<div class="row-fluid">';
		$display .= '<h3>'.$contest_with_prize['title'].'</h3>';

		$display .= '<div class="content_prize span8">';

		foreach ( $contest_with_prize['prizes_data'] as $prize ) 
		{	
			$display .= '<div class="span6 prize">';
			$display .= '<h4>'.$prize['title'].'</h4>';

			for ( $i = 0; $i < count($prize); $i++ )
			{
				$n = $i * 100;
				$u = ($i + 1) * 100;

				if ( $n < $prize['id'] && $prize['id'] < $u )
				{
					$folderName = $n.'-'.$u.'/';
					break;
				}
			}

			$display .= '<img src="'.base_url() . THUMB_IMG . $folderName . $prize['id'] .'.jpg" class="img-polaroid" title="'.$prize['title'].'" style="max-width: 128px;max-height:128px;"/>';
			$display .= '<p class="valeur">'.$prize['value'].'&euro;</p>';

			if ( $this->session->userdata('Connected') ) 
			{
				$display .= '<div class="action actionPrize">';
				$display .=  anchor('admin/updateView/'.$prize['id'].'/prize', '<span>Modifier ce cadeau</span>', array('title' => 'Modifier ce cadeau', 'class' => 'icon-edit')); 
				$display .=  anchor('admin/deleteView/'.$prize['id'].'/prize', '<span>Supprimer ce cadeau</span>', array('title' => 'Supprimer ce cadeau', 'class' => 'icon-cancel')); 
				$display .= '</div>';
			}
			$display .= '</div>';
		}

		$display .= '</div>';

		$display .= '<div class="astuces span4" >';
		$display .= '<h4>Astuces</h4>';

		$display .= '<ul>';

			$astuces = explode("\n", $contest_with_prize['text']);
			for ( $i = 0; $i < count($astuces); $i++) 
			{
				$display .= '<li>'.$astuces[$i].'</li>';
			}

		$display .= '</ul>';

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
		$display .= '</div>';
		$display .= '</article>';

	endforeach;

	echo $display; 
?>

</div>