<?php
	
	/* 
		StigAds - 15 avril 2010 (londnoir@sdmedia.be)
		
		Page d'accueil de l'administration
		
		stigmatix 2010
	*/
	
	require_once('./w_config/config.php');
	
	require_once('./r_includes/phphack.php');
	require_once('./r_includes/class.mbus.php');
	require_once('./r_includes/class.dbaccess.php');
	require_once('./r_includes/class.stdhtml.php');
	require_once('./r_includes/class.stduser.php');
	require_once('./r_includes/functions.main.php');
	
	$mBus = new mbus();
	$db = new dbaccess($mBus);
	
	$user = new stduser($mBus, $db);
	$user->checkpoint(255, _SITE_URL.'gateway.php?mode=login');
	
	$HTMLContent = NULL;
	
	/* HTML document construction. */
	$page = new stdhtml($mBus, 'Jeuxconcours.be', 'admin');
	$page->store($HTMLContent, 'content');
	$page->store_module('menu', 'menu');
	echo $page->get_html();
	
?>
