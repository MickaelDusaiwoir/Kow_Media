<?php
	
	/*
		Konkours.com. Version du fichier : 19 janvier 2011
		(londnoir@sdmedia.com)
		
		Rercherche d'une image via google
		
		Copyright 2011. kowmedia.com
	*/

	require_once('../config/config.php');
	require_once('../include/class.mbus.php');
	require_once('../include/class.dbaccess.php');
	require_once('../include/functions.main.php');
	require_once('./include/functions.tools.php');
	require_once('../include/googleimage.class.php');
	require_once('../include/image.class.php');
	
	$mBus = new mbus();
	$db = new dbaccess($mBus);
	
	$GLOBALS['only_form'] = get_value('only_form', 'gp', 'bool');
	$GLOBALS['no_als'] = true;
	$GLOBALS['disable_level_up_check'] = true;
	
	/* Vérifie la connexion de l'utilisateur */
	require_once('../include/authcheck.php');
	
	/* Vire les non-admins */
	checkpoint(127);
	
	$showForm = true;
	$baseSearch = null;
	
	/* Recherche des informations de l'élément a imager */
	if ( $id = get_value('id', 'gp', 'uint') )
	{
		$search = true;
		
		$type = get_value('type', 'gp', 'string');
		
		switch ( $type )
		{
			case 'kk_prizes' :
				if ( !($baseSearch = $db->get_result('SELECT `title` FROM `kk_prizes` WHERE `id` = '.$id.' LIMIT 1;', 'string')) )
					$mBus->notification(_MSG_ERROR, 'La recherche du titre du lot '.$id.' a échouée!');
				break;
			
			case 'kk_shop' :
				if ( !($baseSearch = $db->get_result('SELECT `title` FROM `kk_shop` WHERE `id` = '.$id.' LIMIT 1;', 'string')) )
					$mBus->notification(_MSG_ERROR, 'La recherche du titre de l\'article '.$id.' a échouée!');
				break;
				
			case 'kk_opinions' :
			case 'kk_bp' :
				break;
			
			default :
				$showForm = false;
				break;
		}
	}
	else
	{
		$showForm = false;
		$search = false;
		$type = null;
		
		$mBus->notification(_MSG_ERROR, 'Il n\'y a pas d\'ID lié!');
	}
	
	if ( get_value('gimg_submitted', 'p', 'bool') )
	{
		$postErrors = 0;
		
		if ( !$url = get_value('g_url', 'p', 'string') )
		{
			$postErrors++;
			
			$mBus->notification(_MSG_ERROR, 'Vous devez choisir un screenshot.');
		}
		
		$x = get_value('x', 'p', 'uint');
		$y = get_value('y', 'p', 'uint');
		$w = get_value('w', 'p', 'uint');
		$h = get_value('h', 'p', 'uint');
		$originalRatio = get_value('original_ratio', 'p', 'float');
		
		$parametric = ( $w && $h ) ? true : false;
		
		if ( !$postErrors )
		{
			$showForm = false;
			
			/* Chemin du fichier temporaire */
			$tmpImagePath = _SITE_PATH;
			switch ( $type )
			{
				case 'kk_prizes' :
					$tmpImagePath .= 'images/';
					break;
				
				case 'kk_opinions' :
					$tmpImagePath .= 'images_op/';
					break;
				
				case 'kk_bp' :
					$tmpImagePath .= 'images_bp/';
					break;
				
				case 'kk_shop' :
					$tmpImagePath .= 'images_shop/';
					break;
				
				default :
					$mBus->add(_MSG_ERROR, 'Mauvais type de fichier');
					echo 'Mauvais type de fichier';
					exit(1);
					break;
			}
			$tmpImagePath .= 'tmp'.md5(time());
			
			/* on télécharge le fichier au chemin temporaire */
			if ( download($url, $tmpImagePath) )
			{
				$error = false;
				
				$mBus->notification(_MSG_INFO, 'Image source "'.$tmpImagePath.'"');
				
				/* Création du répertoire d'accueil. */
				if ( $path = get_img_path($id, $type) )
				{
					if ( !build_path($path) )
					{
						$error = true;
						$mBus->notification(_MSG_ERROR, 'Le répertoire '.$path.' n\'est pas utilisable !');
					}
				}
				else
				{
					$error = true;
					$mBus->add(_MSG_ERROR, 'Mauvais type de fichier');
				}
				
				/* Si le répertoire d'accueil est bon ... */
				if ( !$error )
				{
					$thumb = new Image($tmpImagePath);
					$thumb->setBackgroundColor(array(1.0, 1.0, 1.0));
				
					if ( $parametric )
					{
						$x *= $originalRatio;
						$y *= $originalRatio;
						$w *= $originalRatio;
						$h *= $originalRatio;
						
						if ( $thumb->parametricResize($x, $y, $w, $h, 128, 128, $path.$id.'_128.jpg') > 0 )
						{
							$error = true;
							$mBus->notification(_MSG_ERROR, 'Le screenshot 128x128 n\'est pas enregistré.');
						}
					
						if ( $thumb->parametricResize($x, $y, $w, $h, 64, 64, $path.$id.'_64.jpg') > 0 )
						{
							$error = true;
							$mBus->notification(_MSG_ERROR, 'Le screenshot 64x64 n\'est pas enregistré.');
						}
					}
					else
					{
						if ( $thumb->resize(128, 128, $path.$id.'_128.jpg') > 0 )
						{
							$error = true;
							$mBus->notification(_MSG_ERROR, 'Le screenshot 128x128 n\'est pas enregistré.');
						}
					
						if ( $thumb->resize(64, 64, $path.$id.'_64.jpg') > 0 )
						{
							$error = true;
							$mBus->notification(_MSG_ERROR, 'Le screenshot 64x64 n\'est pas enregistré.');
						}
					}
				
					/* Notification de l'image pour les temoignages */
					if ( !$error )
					{
						$mBus->notification(_MSG_SUCCESS, 'Le screenshot est enregistré et redimensionné.');
						
						if ( $type == 'kk_opinions' )
							$db->execute('UPDATE `kk_opinions` SET `is_img` = 1 WHERE `id` = '.$id.' LIMIT 1;');
					}
				}
				else
				{
					$mBus->notification(_MSG_ERROR, 'Le répertoire n\'a pas pu être créé, annulation ...');
				}
				
				/* On retire l'image temporaire. */
				unlink($tmpImagePath);
			}
			else
			{
				$mBus->notification(_MSG_ERROR, 'Le téléchargement du fichier "'.$url.'" a échoué.');
			}
		}
	}
		
	if ( !function_exists('curl_init') )
	{
		$search = false;
		$mBus->notification(_MSG_ERROR, 'Il manque l\'extension cURL de php pour que la recherche dans google image fonctionne.');
	}
	
	$copyrightBlackList = array(
		'photobucket',
		'imageshack',
		'flickr'
	);
	
	/* Searching on Google */
	$showResult = false;
	$googleImages = array();
	if ( $search )
	{
		$gi = new GoogleImage(GoogleImage::SIZE_LARGE, true);
	
		if ( $gi->search(get_value('words', 'p', 'string', $baseSearch)) > 0 )
		{
			$googleImages = $gi->getResults();
			
			$showResult = true;
		}
		else
		{
			$mBus->notification(_MSG_ERROR, 'La page ne contient aucun résultat !');
		}
	}
	
	/* HTML STARTS HERE */
	$GLOBALS['_jcrop'] = true;
	require_once('./include/html_admin_header.php');
	
	if ( $GLOBALS['only_form'] && ($type == 'kk_prizes') )
	{
		/* HACK : pour l'ajout de prix par dessus le formulaire
		d'un concours (pop-up), on vérifie a plusieurs reprise que l'ID du
		prix figure bien dans le formulaire de la page parente. */
		echo 
			"\t\t\t\t".'<script type="text/javascript">'."\n".
			"\t\t\t\t\t".'window.onload = function initHTML ()'."\n".
			"\t\t\t\t\t".'{'."\n".
			"\t\t\t\t\t\t".'var ids = window.opener.document.getElementById(\'prize_id\');'."\n".
			"\t\t\t\t\t\t".'var id = '.$id.';'."\n".
			"\t\t\t\t\t\t".'if (ids.value) {if (!ids.value.match(id)) {ids.value += \',\'+id}}'."\n".
			"\t\t\t\t\t\t".'else {ids.value = id}'."\n".
			"\t\t\t\t\t".'}'."\n".
			"\t\t\t\t".'</script>'."\n";
	}
	
	switch ( $type )
	{
		case 'kk_prizes' :
			echo "\t\t\t\t".'<h2>Sélection d\'une image pour le lot #'.$id.'</h2>'."\n";
			break;
			
		case 'kk_opinions' :
			echo "\t\t\t\t".'<h2>Sélection d\'une image pour le témoignage #'.$id.'</h2>'."\n";
			break;
			
		case 'kk_shop' :
			echo "\t\t\t\t".'<h2>Sélection d\'une image pour l\'article #'.$id.'</h2>'."\n";
			break;
			
		case 'kk_bp' :
			echo "\t\t\t\t".'<h2>Téléchargement d\'une image pour le bon plan #'.$id.'</h2>'."\n";
			break;
			
		default :
			echo "\t\t\t\t".'<h2>Sélection d\'une image l\'élement inconnu #'.$id.'</h2>'."\n";
			break;
	}
	
	/* Affichage des messages */
	if ( $tmp = $mBus->get_notifications() )
	{
		echo 
			"\t\t\t\t".'<div class="message">'."\n".
			$tmp.
			"\t\t\t\t".'</div>'."\n";
	}
	
	if ( $showForm )
	{
		echo 
			"\t\t\t\t".'<div class="form_box">'."\n".
			"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin_images_chooser.php?id='.$id.'&amp;type='.$type.'" method="post">'."\n".
			"\t\t\t\t\t\t".'<div class="form_section">'."\n".
			"\t\t\t\t\t\t\t".'<label for="words">Effectuer une avec les termes suivants</label><br />'."\n".
			"\t\t\t\t\t\t\t".'<input type="text" id="words" name="words" value="" />'."\n".
			"\t\t\t\t\t\t\t".'<input type="hidden" name="only_form" value="'.$GLOBALS['only_form'].'" />'."\n".
			"\t\t\t\t\t\t\t".'<input type="submit" value="Rechercher" />'."\n".
			"\t\t\t\t\t\t".'</div>'."\n".
			"\t\t\t\t\t".'</form>'."\n".
			"\t\t\t\t\t".'<p class="cmd" style="font-size:14px">&bull; <a class="link" href="'._SITE_URL.'admin/admin_images_upload.php?id='.$id.'&amp;type='.$type.'">Uploader manuellement une image</a></p>'."\n".
			"\t\t\t\t".'</div>'."\n".
			"\t\t\t\t".'<br />'."\n";
		
		if ( $showResult )
		{
			echo
				"\t\t\t\t".'<div class="form_box">'."\n".
				"\t\t\t\t\t".'<h2>Résultat des images trouvées sur Google.be</h2>'."\n".
'					<script type="text/javascript">
						function getJcrop (url, width, height, originalRatio)
						{
							var dim = Math.round(128 / originalRatio);
							var html = "<img id=\"jcrop_target\" src=\"" + url + "\" width=\"" + width + "\" height=\"" + height + "\" alt=\"Si l\'image ne s\'affiche, c\'est que le lien de Google est probablement mort.\" />";
							
							$("#jcrop_img").html(html).css("width", width + "px").css("height", height + "px").css("margin", Math.round((600 - height) / 2) + "px auto 0 auto");
				
							$("#jcrop_target").Jcrop({
								onChange: showCoords,
								onSelect: showCoords,
								minSize: [dim, dim],
								aspectRatio: 1
							});
							
							$("#original_ratio").val(originalRatio);
							
							$("#jcrop_layer").css("display", "block");
							
							window.location.href = "#jcrop_layer";
						}
						
						function closeJcrop ()
						{
							$("#jcrop_layer").css("display", "none");
							$("#jcrop_img").html("");
						}
						
						function showCoords (c)
						{
							$("#x").val(c.x);
							$("#y").val(c.y);
							$("#w").val(c.w);
							$("#h").val(c.h);
						};
					</script>'."\n";
			
			echo 
				"\t\t\t\t\t".'<div id="jcrop_layer" style="width:800px;height:680px;background-color:#EEF;border:8px solid #000;position:absolute;top:center;left:center;display:none">'."\n".
				"\t\t\t\t\t\t".'<div style="width:800px;height:600px;overflow:hidden">'."\n".
				"\t\t\t\t\t\t\t".'<div id="jcrop_img"></div>'."\n".
				"\t\t\t\t\t\t".'</div>'."\n".
				"\t\t\t\t\t\t".'<p style="text-align:center">NOTE : Choisissez une zone intéressante sur l\'image avec la souris.</p>'."\n".
				"\t\t\t\t\t\t".'<p style="text-align:center">'."\n".
				"\t\t\t\t\t\t\t".'<input type="button" value="Sélectionner cette image" onclick="javascript: $(\'#img_form\').submit();" /> '."\n".
				"\t\t\t\t\t\t\t".'<input type="button" value="Choisir une autre" onclick="javascript:closeJcrop();" />'."\n".
				"\t\t\t\t\t\t".'</p>'."\n".
				"\t\t\t\t\t".'</div>'."\n".
				
				"\t\t\t\t\t".'<form id="img_form" action="'._SITE_URL.'admin/admin_images_chooser.php" method="post">'."\n".
				"\t\t\t\t\t\t".'<div class="form_section">'."\n".
				"\t\t\t\t\t\t\t".'<label for="screenshot">Sélectionner une vignette.</label><br />'."\n";
		
			$rowCompleted = true;
			$cell = 0;
			foreach ( $googleImages as $img )
			{
				if ( is_int($cell / 4) )
				{
					echo "\t\t\t\t\t\t\t".'<div class="img_row">'."\n";
					$rowCompleted = false;
				}
				
				$originalRatio = 1.0;
				if ( $img['width'] > $img['height'] )
				{
					/* landscape */
					if ( $img['width'] > 800 )
						$originalRatio = $img['width'] / 800;
				}
				else
				{
					/* portrait */
					if ( $img['height'] > 600 )
						$originalRatio = $img['height'] / 600;
				}
				
				$width = round($img['width'] / $originalRatio);
				$height = round($img['height'] / $originalRatio);
				
				echo 
					"\t\t\t\t\t\t\t\t".'<div class="img_cell">'."\n".
					"\t\t\t\t\t\t\t\t\t".'<div class="img_shell">'."\n".
					"\t\t\t\t\t\t\t\t\t\t".'<img src="'.$img['thumb'].'" alt="Google Thumb" />'."\n".
					"\t\t\t\t\t\t\t\t\t".'</div>'."\n".
					"\t\t\t\t\t\t\t\t\t".'<input class="img_cell" type="radio" name="g_url" value="'.$img['url'].'" onclick="javascript: getJcrop(\''.$img['url'].'\', '.$width.', '.$height.', '.$originalRatio.');" />'."\n".
					"\t\t\t\t\t\t\t\t".'</div>'."\n";
		
				$cell++;
		
				if ( is_int($cell / 4) )
				{
					$rowCompleted = true;
					
					echo 
						"\t\t\t\t\t\t\t\t".'<div class="float_breaker"></div>'."\n".
						"\t\t\t\t\t\t\t".'</div>'."\n";
				}
			}
	
			if ( !$rowCompleted )
			{
				echo 
					"\t\t\t\t\t\t\t\t".'<div class="float_breaker"></div>'."\n".
					"\t\t\t\t\t\t\t".'</div>'."\n";
		
				$rowCompleted = true;
			}

			echo 
				"\t\t\t\t\t\t".'</div>'."\n".
				
				"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
				"\t\t\t\t\t\t\t".'<input type="hidden" name="id" value="'.$id.'" />'."\n".
				"\t\t\t\t\t\t\t".'<input type="hidden" name="only_form" value="'.$GLOBALS['only_form'].'" />'."\n".
				"\t\t\t\t\t\t\t".'<input type="hidden" name="type" value="'.$type.'" />'."\n".
				
				"\t\t\t\t\t\t\t".'<input type="hidden" id="x" name="x" value="0" />'."\n".
				"\t\t\t\t\t\t\t".'<input type="hidden" id="y" name="y" value="0" />'."\n".
				"\t\t\t\t\t\t\t".'<input type="hidden" id="w" name="w" value="0" />'."\n".
				"\t\t\t\t\t\t\t".'<input type="hidden" id="h" name="h" value="0" />'."\n".
				"\t\t\t\t\t\t\t".'<input type="hidden" id="original_ratio" name="original_ratio" value="1.0" />'."\n".
				
				"\t\t\t\t\t\t\t".'<input type="hidden" name="gimg_submitted" value="1" />'."\n".
				
				"\t\t\t\t\t\t\t".'<input type="submit" value="Sélectionner cette image pour le lot" />'."\n".
				"\t\t\t\t\t\t".'</div>'."\n".
				"\t\t\t\t\t".'</form>'."\n".
				"\t\t\t\t".'</div>'."\n";
		}
	}
	else
	{
		echo "\t\t\t\t".'<div class="form_box">'."\n";
		
		if ( $GLOBALS['only_form'] )
		{
			echo 
				"\t\t\t\t\t".'<div class="form_section_c">'."\n".
				"\t\t\t\t\t\t".'<input type="button" value="Fermer la fenêtre" onclick="javascript:window.close();" />'."\n".
				"\t\t\t\t\t".'</div>'."\n";
		}
		else
		{
			switch ( $type )
			{
				case 'kk_prizes' :
					echo 
						"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin_prizes_tools.php" method="get">'."\n".
						"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
						"\t\t\t\t\t\t\t".'<input type="hidden" name="id" value="'.$id.'" />'."\n".
						"\t\t\t\t\t\t\t".'<input type="submit" value="Retourner sur la page du lot" />'."\n".
						"\t\t\t\t\t\t".'</div>'."\n".
						"\t\t\t\t\t".'</form>'."\n".
						
						"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin_prizes_list.php#element_'.$id.'" method="get">'."\n".
						"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
						"\t\t\t\t\t\t\t".'<input type="submit" value="Liste des lots" />'."\n".
						"\t\t\t\t\t\t".'</div>'."\n".
						"\t\t\t\t\t".'</form>'."\n";
					break;
			
				case 'kk_opinions' :
					echo 
						"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin_opinions_tools.php" method="get">'."\n".
						"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
						"\t\t\t\t\t\t\t".'<input type="hidden" name="id" value="'.$id.'" />'."\n".
						"\t\t\t\t\t\t\t".'<input type="submit" value="Retourner sur la page du témoignage" />'."\n".
						"\t\t\t\t\t\t".'</div>'."\n".
						"\t\t\t\t\t".'</form>'."\n".
						
						"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin_opinions_list.php#element_'.$id.'" method="get">'."\n".
						"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
						"\t\t\t\t\t\t\t".'<input type="submit" value="Liste des témoignages" />'."\n".
						"\t\t\t\t\t\t".'</div>'."\n".
						"\t\t\t\t\t".'</form>'."\n";
					break;
					
				case 'kk_shop' :
					echo 
						"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin_shop_tools.php" method="get">'."\n".
						"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
						"\t\t\t\t\t\t\t".'<input type="hidden" name="id" value="'.$id.'" />'."\n".
						"\t\t\t\t\t\t\t".'<input type="submit" value="Retourner sur la page de  l\'article" />'."\n".
						"\t\t\t\t\t\t".'</div>'."\n".
						"\t\t\t\t\t".'</form>'."\n".
						
						"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin_shop_list.php#element_'.$id.'" method="get">'."\n".
						"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
						"\t\t\t\t\t\t\t".'<input type="submit" value="Liste des articles de la boutique" />'."\n".
						"\t\t\t\t\t\t".'</div>'."\n".
						"\t\t\t\t\t".'</form>'."\n";
					break;
			
				default :
					echo 
						"\t\t\t\t\t".'<form action="'._SITE_URL.'admin/admin.php" method="get">'."\n".
						"\t\t\t\t\t\t".'<div class="form_section_c">'."\n".
						"\t\t\t\t\t\t\t".'<input type="submit" value="Retour" />'."\n".
						"\t\t\t\t\t\t".'</div>'."\n".
						"\t\t\t\t\t".'</form>'."\n";
					break;
			}
		}
		
		echo "\t\t\t\t".'</div>'."\n";
	}
	
	require_once('./include/html_admin_footer.php');
	
