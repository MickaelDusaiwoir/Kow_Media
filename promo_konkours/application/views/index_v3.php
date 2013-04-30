<?php 
	// On crée l'affichage des concours pour ce faire, on parcour le tableau $contests_with_prizes qui comporte tout les concours
	// ainsi que tous les cadeaux qui lui sont associés.
	// On crée la variable display qui sera remplie à chaque iteration de la boucle.
	// Dans la première boucle se situe une seconde qui en fonction du concours ajoute les cadeaux qui lui sont associés.
	
	$display = '';
	$prizesTotal = 0;

	$countContest = 1;

	foreach ( $contests_with_prizes as $contest_with_prize ) : 



		// Parcour des cadeaux
		foreach ( $contest_with_prize['prizes_data'] as $prize ) 
		{	
			// On calcule dans quel dossier se trouve l'image.
			$thumbnail = getPath('thumbnail', $prize['id']);

			// on retire les caractères inutiles
			$tmp = explode('../', $thumbnail);

			// Déclaration du titre, de la valeur et de l'image du cadeau.
			$display .= '<figure>'.
							'<a href="#modal-formulaire" data-id="'.$contest_with_prize['id'].'" rel="'.$contest_with_prize['url'].'" role="button" data-toggle="modal" title="'.$contest_with_prize['title'].'" class="img">'.
								'<img src="'. base_url() . $tmp[0] . $prize['id'] .'.jpg" alt="'.$prize['title'].'" width="128" max-height="128" />'.
							 
							'<span class="slide-title">'.
							'<h4>'.$prize['title'].'</h4>'.
							'<p class="valeur">'.number_format($prize['value'], 0, ',', ' ').'&euro;</p>'.
							'</span>'.
							'</a>'.
						'</figure>';

			$prizesTotal += $prize['value'];

			// Si on est connecté ont affiche les options d'administrations des cadeaux (modifier / supprimer).
			if ( $this->session->userdata('Connected') ) 
			{
				$display .= '<div class="action actionPrize">';
				$display .=  anchor('admin/updateView/'.$prize['id'].'/prize', '<span>Modifier ce cadeau</span>', array('title' => 'Modifier ce cadeau', 'class' => 'icon-pencil')); 
				$display .=  anchor('admin/deleteView/'.$prize['id'].'/prize', '<span>Supprimer ce cadeau</span>', array('title' => 'Supprimer ce cadeau', 'class' => 'icon-cancel')); 
				$display .= '</div>';
			}
		}

		// Afficahge de l'option ajout d'un cadeau.
		if ( $this->session->userdata('Connected') ) 
		{
			$display .= '<div class="add_gift">';
			$display .= anchor('admin/addPrizesView/'.$contest_with_prize['id'], '<span>Ajoutez un cadeau</span>', array('title' => 'Ajouter un cadeau', 'class' => 'icon-plus')); 
			$display .= '</div>';
		}
		
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
	<em>Astuce&nbsp;: Remplissez <u>une seule fois</u> vos informations pour jouer à tous les concours&nbsp;!</em>
  </p>
  <footer>
	<button class="btn" id="btnYes" data-dismiss="modal" aria-hidden="true">Oui</button>
	<button class="btn" id="btnNo" data-dismiss="modal" aria-hidden="true">Non</button>
  </footer>
</section>

<!--- DEJA JOUER ------->
<section id="modal-played" title="promokonkours.be" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
  <header>
	<a class="closeModal" data-dismiss="modal" aria-hidden="true"><i class="icon-cancel"></i></a>
	<h2>Concours déjà joué..</h2>
	<p>
		<a href="#" title="Ré-ouvrir le concours" target="_blank">Rejouez</a>
	</p>
  </header>
</section>

<!-- FORMMULAIREEE MODALE BOX ----->
<section id="modal-formulaire" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
  <section id="formulaire">
		  <header>
			<h2 id="titleForm">Où envoyer les cadeaux&nbsp;?</h2>
			<a class="closeModal" data-dismiss="modal" aria-hidden="true"><i class="icon-cancel"></i></a>
		  </header>
		  <section id="userData">
			<section id="progress">
				<h1 class="no_show">Progression du formulaire</h1>
				<progress value="0" min="0" max="100"><span></span></progress>
				<p class="pourcentage">
					Remplissage à <span>0</span>%
				</p>
			</section>
			<form method="post" action="#" id="form">
				
				<label for="man" class="radio">Monsieur
					<input type="radio" id="man" name="civilite" value="man"/>
				</label>

				<label for="woman" class="radio">Madame
					<input type="radio" id="woman" name="civilite" value="woman"/>
				</label>

				<label for="miss" class="radio">Mademoiselle
					<input type="radio" id="miss" name="civilite" value="miss"/>
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
						for ( $a = 1920; $a < 2015; $a++ )
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
				
				<input type="submit" class="btn" data-dismiss="modal" aria-hidden="true" value="Valider mes informations" />
			</form>
			<p class="info icon-alert">
				Vos informations seront utilisées pour participer à des concours
			</p>
		 </section>
		 <!-- 
		 <aside>
			<h3>Cadeaux à gagner</h3>
			<div id="thumbs_img"></div>
		 </aside> -->	 
	</section>
	
	<section id="loaderBox">
		<header>
			<p>Etape 1&nbsp;: OK</p>
		 </header>
		<div id="loader">
			<div class="f_circleG" id="frotateG_01">
			</div>
			<div class="f_circleG" id="frotateG_02">
			</div>
			<div class="f_circleG" id="frotateG_03">
			</div>
			<div class="f_circleG" id="frotateG_04">
			</div>
			<div class="f_circleG" id="frotateG_05">
			</div>
			<div class="f_circleG" id="frotateG_06">
			</div>
			<div class="f_circleG" id="frotateG_07">
			</div>
			<div class="f_circleG" id="frotateG_08">
			</div>
		</div>
		<footer>
			<p class="info">
				Etape 2&nbsp;: Choisissez vos cadeaux
			</p>
		</footer>
	</section>
	
</section>

<section id="contests">
	
	<h2 class="active">
		<i class="icon-down"></i> Choissisez vos cadeaux <i class="icon-down"></i>
	</h2>
	<section id="infosUser">
		<a href="#modal-formulaire" role="button" data-toggle="modal" title="Modifiez vos données" id="btn_infos">
			<h1>Vos coordonnées</h1>
			<p>
				<!-- <em>Nom&nbsp;:</em> -->
				<span id="user_civilite">M.</span> <span id="user_nom">Rambo</span> <span id="user_prenom">John</span>
			</p>
			<p>
				<!--<em>Date de naissance&nbsp;:</em>-->
				<span id="user_ddn">1 janvier 1985</span>
			</p>
			<p>
				<!--<em>E-mail&nbsp;:</em>-->
				<span id="user_email">john.rambo@exemple.be</span>
			</p>
			<p>
				<!--<em>Adresse&nbsp;:</em>-->
				<span id="user_adresse">62 rue de Lille</span>
			</p>
			<p>
				<!--<em>CP&nbsp;:</em>-->
				<span id="user_cp">75343</span>, <span id="user_ville">Parisssssssss</span>
			</p>
		</a>
		<footer>
			<h5 id="step1"><em>Etape 1:</em> OK&nbsp;<i class="icon-ok"></i></h5>
			<h5 class="currentStep"><em>Etape 2:</em> Choisissez vos cadeaux&nbsp;<i class="icon-right"></i></h5>
		</footer>
	</section>
	<section id="intro">
		<article class="pub" id="pub_1">
			<header>
				<blockquote>
					<i class="icon-quote-left-1"></i>
						<p class="quote">
							Nous avons séléctionné pour vous <strong><?= count($contests_with_prizes) ?>&nbsp;concours <u>100% GRATUITS&nbsp;!</u></strong>
						</p>
						
					<ol>
						<li>
							Remplissez <strong>une seule fois</strong> le formulaire.
						</li>
						<li>
							Sélectionnez vos cadeaux préférés.
						</li>
					</ol>
					<i class="icon-quote-right-1"></i>
				</blockquote>
			</header>
		</article>
		<article class="pub team" id="pub_2">
				<figure>
					<img src="<?= base_url() ?>/web/img/pierre.jpg" alt="Pierre @ KowMedia" width="78" height="103">
					<p>Pierre</p>
					<img src="<?= base_url() ?>/web/img/sign2.png" class="signature" />
				</figure>
				<figure>
					<img src="<?= base_url() ?>/web/img/ales.jpg" alt="Ales @ KowMedia" width="78" height="103">
					<p>Ales</p>
					<img src="<?= base_url() ?>/web/img/sign1.png" class="signature" />
				</figure>
				<h5>Experts en concours</h5>
		</article>
	</section>
	<?php
		// On affiche les articles.
		echo $display; 
	?>
	
	<section class="no_show" id="footer">
		<a href="http://www.konkours.com" rel="http://www.konkours.com" target="_blank" title="Accéder à Konkours.com">
			<h3>
				Marre de remplir les formulaires à chaque fois&nbsp;? <br /> Le logiciel WahOO le fait pour vous&nbsp;!
			</h3>
			<figure>
				<img src="<?= base_url().IMG_DIR ?>wahoo.png" alt="Logiciel WahOO" width="128" max-height="128">
				<p>100% GRATUIT&nbsp;!</p>
			</figure>
			<ul>
				<li>Les meilleurs concours du Net</li>
				<li>Complète les formulaires</li>
				<li>Sans publicité&nbsp;!</li>
			</ul>
		</a>
	</section>
	
</section>