<section id="dialog-message" title="promokonkours.be" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
  <header>
	PromoKonkours.be
  </header>
  <p>
    Voulez-vous jouer à <strong><?php if ( count($contests_with_prizes) > 1 ) echo 'nos '.count($contests_with_prizes); else echo 'notre ';?></strong> concours&nbsp; <u>en moins de 10&nbsp;minutes</u>&nbsp;?
  </p>
  <p>
	<strong>C'est très simple&nbsp!</strong> Remplissez <em>une SEULE fois le formulaire</em> et <em>jouez <?= count($contests_with_prizes) ?> fois</em>.
  </p>
  <footer>
	<button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>
  </footer>
</section>

<div id="userData">
	<h2>
		1. Complétez 1X
	</h2>
	<form method="post" action="#" id="form">


		<label for="nom">Nom</label>			
		<input id="nom" name="nom" type="text" placeholder="Rambo" />

		<label for="prenom" id="firstname">Pr&eacute;nom</label>
		<input id="prenom" name="prenom" type="text" placeholder="John" />

		<label for="man" class="radio">Monsieur
			<input type="radio" id="man" name="civilite" value="man"/>
		</label>

		<label for="woman" class="radio">Madame
			<input type="radio" id="woman" name="civilite" value="woman"/>
		</label>

		<label for="miss" class="radio">Mademoiselle
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

	<div id="progress">
		<p>0%</p>
		<progress value="5" min="0" max="100"></progress>
	</div>

</div>

<div id="contests">
<h2>
	2. Jouez <?= count($contests_with_prizes) ?>X
</h2>

<?php 
	// On crée l'affichage des concours pour ce faire, on parcour le tableau $contests_with_prizes qui comporte tout les concours
	// ainsi que tous les cadeaux qui lui sont associés.
	// On crée la variable display qui sera remplie à chaque iteration de la boucle.
	// Dans la première boucle se situe une seconde qui en fonction du concours ajoute les cadeaux qui lui sont associés.

	$display = '';

	$countContest = 1;

	foreach ( $contests_with_prizes as $contest_with_prize ) : 

		// Déclaration du conteneur et du titre du concours.
		$display .= '<article class="contest">';
		$display .= '<header><h3><a href="'.$contest_with_prize['url'].'title="Cliquez pour participer à ce concours">Concours GRATUIT ' . $countContest.'&nbsp;: '. $contest_with_prize['title'] .'</a></h3></header>';

		$display .= '<div class="content_prize">';

		// Parcour des cadeaux
		foreach ( $contest_with_prize['prizes_data'] as $prize ) 
		{	
			$display .= '<section class="prize">';

			// On calcule dans quel dossier se trouve l'image.
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

			// Déclaration du titre, de la valeur et de l'image du cadeau.
			$display .= '<figure><a href="'.$contest_with_prize['url'].'title="Cliquez pour participer à ce concours"><img src="'.base_url() . THUMB_IMG . $folderName . $prize['id'] .'.jpg" title="'.$prize['title'].'" style="max-width: 128px;max-height:128px;"/></a></figure>';
			$display .= '<h4>'.$prize['title'].'</h4>';
			$display .= '<p class="valeur">'.$prize['value'].'&euro;</p>';

			// Si on est connecté ont affiche les options d'administrations des cadeaux (modifier / supprimer).
			if ( $this->session->userdata('Connected') ) 
			{
				$display .= '<div class="action actionPrize">';
				$display .=  anchor('admin/updateView/'.$prize['id'].'/prize', '<span>Modifier ce cadeau</span>', array('title' => 'Modifier ce cadeau', 'class' => 'icon-pencil')); 
				$display .=  anchor('admin/deleteView/'.$prize['id'].'/prize', '<span>Supprimer ce cadeau</span>', array('title' => 'Supprimer ce cadeau', 'class' => 'icon-cancel')); 
				$display .= '</div>';
			}
			$display .= '</section>';
		}

		// Afficahge de l'option ajout d'un cadeau.
		if ( $this->session->userdata('Connected') ) 
		{
			$display .= '<div class="add_gift">';
			$display .= anchor('admin/addPrizesView/'.$contest_with_prize['id'], '<span>Ajouter un cadeau</span>', array('title' => 'Ajouter un cadeau', 'class' => 'icon-plus')); 
			$display .= '</div>';
		}

		$display .= '</div>';

		// 	On découpe les astuces afin de les placer individuellement dans un li.
		$display .= '<aside class="astuces" >';

		$display .= '<ol>';

			$astuces = explode("\n", $contest_with_prize['text']);
			for ( $i = 0; $i < count($astuces); $i++) 
			{
				$display .= '<li><i></i><span>'.$astuces[$i].'</span></li>';
			}

		$display .= '</ol>';

		$display .= anchor($contest_with_prize['url'], 'Je valide&nbsp;!', array('title' => 'Cliquez ICI pour participer à ce concours', 'class' => 'btn'));

		// Si on est connecté ont affiche les options d'administrations des concours (modifier / supprimer).
		if ( $this->session->userdata('Connected') ) 
		{
			$display .= '<div class="action actionContest">';
			$display .= anchor('admin/updateView/'.$contest_with_prize['id'].'/contest', '<span>Modifier ce concours</span>', array('title' => 'Modifier ce concours', 'class' => 'icon-pencil')); 
			$display .= anchor('admin/deleteView/'.$contest_with_prize['id'].'/contest', '<span>Supprimer ce concours</span>', array('title' => 'Supprimer ce concours', 'class' => 'icon-cancel')); 
			$display .= '</div>';
		}
		$display .= '</aside>';
		$display .= '</article>';		

		$countContest += 1;

	endforeach;

	// On affiche les articles.
	echo $display; 
?>

</div>