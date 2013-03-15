<div id="userData">
	<h2>
		1. Complétez 1X
	</h2>
	<form method="post" action="#" id="form">


		<label for="nom">Nom</label>			
		<input id="nom" name="nom" type="text" placeholder="Rambo" />

		<label for="prenom" id="firstname">Pr&eacute;nom</label>
		<input id="prenom" name="prenom" type="text" placeholder="John" />

		<label for="man" class="radio">M.
			<input type="radio" id="man" name="civilite" value="man"/>
		</label>

		<label for="woman" class="radio">Mme
			<input type="radio" id="woman" name="civilite" value="woman"/>
		</label>

		<label for="miss" class="radio">Mlle
			<input type="radio" id="miss" name="civilite" value="miss"/>
		</label>

		<label  id="ddn">Date de naissance</label>
		<select id="jour" name="jour" ></select>
		<select id="mois" name="mois" ></select>
		<select id="annee" name="annee" ></select>

		<label for="email">E-mail</label>
		<input id="email" name="email" type="email" placeholder="john.rambo@exemple.com" />

		<label for="add">Adresse</label>
		<input id="adresse" name="adresse" type="text" placeholder="Rue du champ de mine n°1" />

		<label for="cp">CP</label>
		<input id="code_postal" name="code_postal" type="text" placeholder="4000" />

		<label for="ville" id="city">Ville</label>
		<input id="ville" name="ville" type="text" placeholder="Li&egrave;ge" />
	
	</form>
</div>

<div id="contests">
<h2>
	2. Jouez 10X
</h2>

<?php 
	
	$display = '';

	foreach ( $contests_with_prizes as $contest_with_prize ) : 

		$display .= '<article class="contest">';
		$display .= '<header><h3>'.$contest_with_prize['title'].'</h3></header>';

		$display .= '<div class="content_prize">';

		foreach ( $contest_with_prize['prizes_data'] as $prize ) 
		{	
			$display .= '<section class="prize">';

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

			$display .= '<figure><img src="'.base_url() . THUMB_IMG . $folderName . $prize['id'] .'.jpg" title="'.$prize['title'].'" style="max-width: 128px;max-height:128px;"/></figure>';
			$display .= '<h4>'.$prize['title'].'</h4>';
			$display .= '<p class="valeur">'.$prize['value'].'&euro;</p>';

			if ( $this->session->userdata('Connected') ) 
			{
				$display .= '<div class="action actionPrize">';
				$display .=  anchor('admin/updateView/'.$prize['id'].'/prize', '<span>Modifier ce cadeau</span>', array('title' => 'Modifier ce cadeau', 'class' => 'icon-edit')); 
				$display .=  anchor('admin/deleteView/'.$prize['id'].'/prize', '<span>Supprimer ce cadeau</span>', array('title' => 'Supprimer ce cadeau', 'class' => 'icon-cancel')); 
				$display .= '</div>';
			}
			$display .= '</section>';
		}

		$display .= '<div class="add_gift">';
		$display .= anchor('admin/addPrizesView/'.$contest_with_prize['id'], '<span>Ajouter un cadeau</span>', array('title' => 'Ajouter un cadeau', 'class' => 'icon-plus')); 
		$display .= '</div>';

		$display .= '</div>';

		$display .= '<aside class="astuces" >';

		$display .= '<ol>';

			$astuces = explode("\n", $contest_with_prize['text']);
			for ( $i = 0; $i < count($astuces); $i++) 
			{
				$display .= '<li><i></i><span>'.$astuces[$i].'</span></li>';
			}

		$display .= '</ol>';

		$display .= anchor($contest_with_prize['url'], 'Je valide&nbsp;!', array('title' => 'Cliquez ICI pour jouer à ce concours', 'class' => 'btn'));

		if ( $this->session->userdata('Connected') ) 
		{
			$display .= '<div class="action actionContest">';
			$display .= anchor('admin/updateView/'.$contest_with_prize['id'].'/contest', '<span>Modifier ce concours</span>', array('title' => 'Modifier ce concours', 'class' => 'icon-edit')); 
			$display .= anchor('admin/deleteView/'.$contest_with_prize['id'].'/contest', '<span>Supprimer ce concours</span>', array('title' => 'Supprimer ce concours', 'class' => 'icon-cancel')); 
			$display .= '</div>';
		}
		$display .= '</aside>';
		$display .= '</article>';

	endforeach;

	echo $display; 
?>

</div>