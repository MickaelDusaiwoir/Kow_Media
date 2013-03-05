<?php
	
	/* 
		jeuxconcours.be - 16 juin 2010 (londnoir@sdmedia.be)
		
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
	
	$contestID = get_value('id', 'g', 'uint');
	
	$HTMLContent = NULL;
	if ( $contestID )
	{
		$list = new stdlist($mBus, $db, 'jc_prizes');
		$list->set_title('Liste des cadeaux du concours #'.$contestID);
		$list->set_sql_where('`contest_id` = '.$contestID.' ');
		
		$list->set_select_fields(array('id', 'position', 'title', 'value'));
		$list->add_quick_column('ID', 'id', 48);
		$list->add_quick_column('POS.', 'position', 48);
		$list->add_quick_column('TITRE', 'title', 256);
		$list->add_quick_column('VALEUR', 'value', 160);
		
		$list->add_column('OUTILS', 256, TRUE);
		$tools  = '<a href="'._SITE_URL.'admin_prizes_tools.php?action=load&amp;cid='.$contestID.'&amp;id={id}" title="Editer ce cadeau">éditer</a>';
		$tools .= ' - <a href="'._SITE_URL.'admin_prizes_tools.php?action=delete&amp;cid='.$contestID.'&amp;id={id}" title="Supprimer ce cadeau" onclick="javascript: return confirm(\'Etes-vous sur de vouloir supprimer ce lot ?\');">supprimer</a>';
		$list->set_column_parsed_data($tools);
		
		$HTMLContent .= $list->get_html();
	}
	
	/* HTML document construction. */
	$page = new stdhtml($mBus, 'Jeuxconcours.be', 'admin');
	$page->store($HTMLContent, 'content');
	if ( $contestID )
		$page->store("\t\t\t\t".'<p class="cmd"><a class="link" href="'._SITE_URL.'admin_prizes_tools.php?cid='.$contestID.'" title="Ajouter un concours">Ajouter</a></p>'."\n", 'content');
	$page->store_module('menu', 'menu');
	echo $page->get_html();
	
?>
