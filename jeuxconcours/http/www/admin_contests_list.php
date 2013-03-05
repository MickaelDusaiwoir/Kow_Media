<?php
	
	/* 
		jeuxconcours.be - 9 juin 2010 (londnoir@sdmedia.be)
		
		Liste des concours.
		
		stigmatix 2010
	*/
	
	require_once('./w_config/config.php');
	
	require_once('./r_includes/phphack.php');
	require_once('./r_includes/class.mbus.php');
	require_once('./r_includes/class.dbaccess.php');
	require_once('./r_includes/class.stdhtml.php');
	require_once('./r_includes/class.stdlist.php');
	require_once('./r_includes/class.stduser.php');
	require_once('./r_includes/functions.main.php');
	
	$mBus = new mbus();
	$db = new dbaccess($mBus);

	$user = new stduser($mBus, $db);
	$user->checkpoint(255, _SITE_URL.'gateway.php?mode=login');
	
	$list = new stdlist($mBus, $db, 'jc_contests');
	$list->set_title('Liste des concours');
	$list->set_select_fields(array('id', 'position', 'title', 'url', 'url_alt', 'status'));
	$list->add_quick_column('ID', 'id', 48);
	$list->add_quick_column('POS.', 'position', 48);
	$list->add_quick_column('TITRE', 'title', 300);
	$list->add_column('LIENS', 256, FALSE);
	if ( strstr(_SITE_URL, 'france') )
		$list->set_column_parsed_data('<a href="{url}" title="{url}">jeuxconcoursfrance.com</a> <a href="{url_alt}" title="{url_alt}">jeuxconcours-france.com</a>');
	else
		$list->set_column_parsed_data('<a href="{url}" title="{url}">jeuxconcours.be</a> <a href="{url_alt}" title="{url_alt}">jeux-concours.be</a>');
	
	$list->add_column('OUTILS', 256, TRUE);
	$tools  = '<a href="'._SITE_URL.'admin_contests_tools.php?action=load&amp;id={id}" title="Editer ce concours">éditer</a>';
	$tools .= ' - <a href="'._SITE_URL.'admin_prizes_list.php?id={id}" title="Voir les prix associer aux concours">lots</a>';
	$tools .= ' - <a href="'._SITE_URL.'admin_contests_tools.php?action=delete&amp;id={id}" title="Supprimer ce concours" onclick="javascript:return confirm(\'Etes-vous sur de vouloir supprimer ce concours ?\');">supprimer</a>';
	$list->set_column_parsed_data($tools);
	
	$options = new column_options ('status');
	$options->add_option(0, ' - <a href="'._SITE_URL.'admin_contests_tools.php?action=enable&amp;id={id}" title="Activer ce concours">activer</a>');
	$options->add_option(1, ' - <a href="'._SITE_URL.'admin_contests_tools.php?action=disable&amp;id={id}" title="Désativer ce concours">désactiver</a>');
	$list->set_column_condition_data($options);
	
	/* HTML document construction. */
	$page = new stdhtml($mBus, 'Jeuxconcours.be', 'admin');
	$page->store($list->get_html(), 'content');
	$page->store("\t\t\t\t".'<p class="cmd"><a class="link" href="'._SITE_URL.'admin_contests_tools.php" title="Ajouter un concours">Ajouter</a></p>'."\n", 'content');
	$page->store_module('menu', 'menu');
	echo $page->get_html();
	
?>
