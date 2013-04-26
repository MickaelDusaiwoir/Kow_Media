<?php 
	// On crée l'affichage des concours pour ce faire, on parcour le tableau $contests_with_prizes qui comporte tout les concours
	// ainsi que tous les cadeaux qui lui sont associés.
	// On crée la variable display qui sera remplie à chaque iteration de la boucle.
	// Dans la première boucle se situe une seconde qui en fonction du concours ajoute les cadeaux qui lui sont associés.
	
	$display = '';
	$prizesTotal = 0;

	$countContest = 1;

	foreach ( $contests_with_prizes as $contest_with_prize ) : 

		// Déclaration du conteneur et du titre du concours.
		$display .= '<article class="contest">';
		$display .= '<header><h3><a href="'.$contest_with_prize['url'].'" title="Cliquez pour participer à ce concours" onclick="javascript:countClick('.$contest_with_prize['id'].'); _gaq.push([\'_trackPageview\',\'/clic.html\']);" target="_blank" >Concours GRATUIT ' . $countContest.'&nbsp;: '. $contest_with_prize['title'] .'</a></h3></header>';

		$display .= '<div class="content_prize">';

		// Parcour des cadeaux
		foreach ( $contest_with_prize['prizes_data'] as $prize ) 
		{	
			$display .= '<section class="prize">';

			// On calcule dans quel dossier se trouve l'image.
			$thumbnail = getPath('thumbnail', $prize['id']);

			// on retire les caractères inutiles
			$tmp = explode('../', $thumbnail);

			// Déclaration du titre, de la valeur et de l'image du cadeau.
			$display .= '<figure><a href="'.$contest_with_prize["url"].'" title="Cliquez pour participer à ce concours" onclick="javascript:countClick('.$contest_with_prize['id'].'); _gaq.push([\'_trackPageview\',\'/clic.html\']);" target="_blank" class="btn_img" ><img src="'. base_url() . $tmp[0] . $prize["id"] .'.jpg" title="'.$prize["title"].'" width="128" max-height="128" /></a></figure>';
			$display .= '<h4>'.$prize['title'].'</h4>';
			$display .= '<p class="valeur">'.number_format($prize['value'], 0, ',', ' ').'&euro;</p>';

			$prizesTotal += $prize['value'];

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
			$display .= anchor('admin/addPrizesView/'.$contest_with_prize['id'], '<span>Ajoutez un cadeau</span>', array('title' => 'Ajouter un cadeau', 'class' => 'icon-plus')); 
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

		$display .= anchor($contest_with_prize['url'], 'Je valide&nbsp;!', array('title' => 'Cliquez ICI pour participer à ce concours', 'class' => 'btn', 'onclick' => 'javascript:countClick('.$contest_with_prize['id'].'); _gaq.push([\'_trackPageview\',\'/clic.html\']);'));

		$display .= '</aside>';

		// Si on est connecté ont affiche les options d'administrations des concours (modifier / supprimer).
		if ( $this->session->userdata('Connected') ) 
		{
			$display .= '<div class="action actionContest">';
			$display .= anchor('admin/updateView/'.$contest_with_prize['id'].'/contest', '<span>Modifier ce concours</span>', array('title' => 'Modifier ce concours', 'class' => 'icon-pencil')); 
			$display .= anchor('admin/deleteView/'.$contest_with_prize['id'].'/contest', '<span>Supprimer ce concours</span>', array('title' => 'Supprimer ce concours', 'class' => 'icon-cancel')); 
			$display .= '</div>';
		}
		
		$display .= '</article>';		

		$countContest += 1;

	endforeach;
?>


<section id="dialog-message" title="promokonkours.be" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
  <header>
	Important&nbsp;!
	<a class="closeModal" data-dismiss="modal" aria-hidden="true"><i class="icon-cancel-1"></i></a>
  </header>
  <p class="question">
    Désirez-vous jouer à <strong><?php if ( count($contests_with_prizes) > 1 ) echo 'nos '.count($contests_with_prizes); else echo 'notre ';?></strong> concours&nbsp; <u>GRATUITS</u>&nbsp;? <em>(<?php echo number_format($prizesTotal, 0,','," "); ?>&nbsp;€ de cadeaux)</em>
  </p>
  <p>
	<em>Astuce&nbsp;:</em> Remplissez <u>une seule fois</u> vos informations pour jouer à tous les concours&nbsp;!
  </p>
  <footer>
	<button class="btn" id="btnYes" data-dismiss="modal" aria-hidden="true">Oui</button>
	<button class="btn" id="btnNo" data-dismiss="modal" aria-hidden="true">Non</button>
  </footer>
</section>

<section id="userData">
	<div class="fixed">
		<h2>
			1. <span>Complétez</span> <em>1X</em>
		</h2>
		<form method="post" action="#" id="form">

			<label for="man" class="radio">Monsieur
				<input type="radio" id="man" name="civilite" value="man"/>
			</label>

			<label for="woman" class="radio">Madame
				<input type="radio" id="woman" name="civilite" value="woman"/>
			</label>

			<label for="miss" class="radio">Mademoiselle
				<input type="radio" id="miss" name="civilite" value="miss" />
			</label>

			<label for="nom">Nom</label>			
			<input id="nom" name="nom" type="text" placeholder="Rambo" />

			<label for="prenom" id="firstname">Pr&eacute;nom</label>
			<input id="prenom" name="prenom" type="text" />

			<label  id="ddn">Date de naissance</label>
			<select id="jour" name="jour" >
				<?php 
					$jour = '';
					for ( $j = 1; $j < 32; $j++ )
						$jour .= '<option value="'.$j.'" name="'.$j.'">'.$j.'</option>';

					echo $jour;
				?>
			</select>
			<select id="mois" name="mois" >
				<?php 
					$mois = array('Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Décembre');
					$options = '';

					for ( $m = 0; $m < count($mois); $m++ )
						$options .= '<option value="'.($m + 1).'" name="'.$mois[$m].'">'.$mois[$m].'</option>';

					echo $options;
				?>
			</select>
			<select id="annee" name="annee" >
				<?php 
					$annee = '';
					for ( $a = 1920; $a < 2030; $a++ )
						$annee .= '<option value="'.$a.'" name="'.$a.'">'.$a.'</option>';

					echo $annee;
				?>
			</select>

			<label for="email">E-mail</label>
			<input id="email" name="email" type="email" />

			<label for="add">Adresse</label>
			<input id="adresse" name="adresse" type="text" />

			<label for="cp">CP</label>
			<input id="code_postal" name="code_postal" type="text" />

			<label for="ville" id="city">Ville</label>
			<input id="ville" name="ville" type="text" />
			
			<button class="btn">Enregistrer</button>

		</form>
	</div>
	<span id="fixedbug"></span>
	<section id="progress">
	<h1 class="no_show">Barre de progression</h1>
		<strong class="current icon-cancel"></strong>		
		<progress value="0" min="0" max="100"><span></span></progress>
		<p class="pourcentage">
			Remplissage à <span>0</span>%
		</p>
		<small class="icon-cancel">Etape 2&nbsp;: Choisissez vos cadeaux</small>
	</section>
	<section id="intro">
		<article class="pub" id="pub_1">
			<header>
			<h1 class="no_show">Introduction</h1>
		<blockquote>
		<i class="icon-quote-left"></i>
			<p class="quote">
				Nous avons séléctionné pour vous <strong><?= count($contests_with_prizes) ?>&nbsp;concours <u>100% GRATUITS&nbsp;!</u></strong>
			</p>
			
		<ol>
			<li>
				Remplissez <strong>UNE SEULE FOIS</strong> le formulaire.
			</li>
			<li>
				Sélectionnez vos cadeaux préférés.
			</li>
		</ol>
		<i class="icon-quote-right"></i>
		</blockquote>
		
			<section id="team">
				
				<figure>
					<img src="<?= base_url() ?>/web/img/pierre.jpg" alt="Pierre @ KowMedia" width="78" height="103">
					<p>Pierre</p>
				</figure>
				<figure>
					<img src="<?= base_url() ?>/web/img/ales.jpg" alt="Ales @ KowMedia" width="78" height="103">
					<p>Ales</p>
				</figure>
				<h5>Nos experts concours</h5>
			</section>
			</header>
			<footer>
				<a href="http://www.konkours.com" title="Visitez Konkours.com" target="_blank" id="website">Konkours.com</a>
			</footer>
		</article>
		<article class="pub" id="pub_2">
			<p>
				It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here'
			</p>
		</article>
	</section>
</section>

<section id="contests">
	<h2>
		2. <span>Jouez</span> <em><?= count($contests_with_prizes) ?>X</em>
	</h2>

	<?php
		// On affiche les articles.
		echo $display; 
	?>

</section>