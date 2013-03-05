<?php 
	
	echo "\t\t\t\t".'<div class="box">'."\n";
	if ( isset($GLOBALS['user']) )
		echo "\t\t\t\t\t".'<p class="text_bold">Bonjour '.$GLOBALS['user']->get_username().', </p>';
	echo "\t\t\t\t\t".'<h2 class="menu">Menu</h2>'."\n";
	echo "\t\t\t\t\t".'<p class="menu_entry"><a class="link" href="'._SITE_URL.'admin.php" title="Accueil de l\'administration">. Accueil</a></p>'."\n";
	
	echo "\t\t\t\t\t".'<h2 class="menu">Concours</h2>'."\n";
	echo "\t\t\t\t\t".'<p class="menu_entry"><a class="link" href="'._SITE_URL.'admin_contests_list.php" title="Voir la liste des concours">Concours</a></p>'."\n";
	echo "\t\t\t\t\t".'<br />'."\n";
	echo "\t\t\t\t\t".'<p class="menu_entry"><a class="link" href="'._SITE_URL.'" title="Retour sur le site">. Quitter</a></p>'."\n";
	echo "\t\t\t\t".'</div>'."\n";
	
?>
