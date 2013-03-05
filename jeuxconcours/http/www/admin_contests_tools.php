<?php
	
	/* 
		StigAds - 16 juin 2010 (londnoir@sdmedia.be)
		
		Ajout/Edition de concours.
		
		stigmatix 2010
	*/
	
	require_once('./w_config/config.php');
	
	require_once('./r_includes/phphack.php');
	require_once('./r_includes/class.mbus.php');
	require_once('./r_includes/class.dbaccess.php');
	require_once('./r_includes/class.stdhtml.php');
	require_once('./r_includes/class.stdform.php');
	require_once('./r_includes/class.stduser.php');
	require_once('./r_includes/functions.main.php');
	
	$mBus = new mbus();
	$db = new dbaccess($mBus);

	$user = new stduser($mBus, $db);
	$user->checkpoint(255, _SITE_URL.'gateway.php?mode=login');
	
	$HTMLBuffer = NULL;
	
	$showForm = TRUE;
	$contest = array();
	
	/* Edition ou ajout */
	if ( $contestID = get_value('id', 'gp', 'uint') )
	{
		$goback = FALSE;
		
		switch ( get_value('action', 'g', 'string') )
		{
			case 'delete' :
				$showForm = FALSE;
				if ( $db->execute('DELETE FROM `jc_contests` WHERE `id` = '.$contestID.' LIMIT 1;') )
				{
					header('Location:'.$_SERVER['HTTP_REFERER']);
					exit(0);
				}
				break;
				
			case 'enable' :
				$showForm = FALSE;
				if ( $db->execute('UPDATE `jc_contests` SET `status` = 1 WHERE `id` = '.$contestID.' LIMIT 1;') )
					$goback = TRUE;
				break;
				
			case 'disable' :
				$showForm = FALSE;
				if ( $db->execute('UPDATE `jc_contests` SET `status` = 0 WHERE `id` = '.$contestID.' LIMIT 1;') )
					$goback = TRUE;
				break;
				
			case 'load' :
			default :
				if ( $contest = $db->get_row('SELECT `position`, `title`, `text`, `url`, `url_alt` FROM `jc_contests` WHERE `id` = '.$contestID.' LIMIT 1;') )
				{
					$contest['position'] = intval($contest['position']);
				}
				else
				{
					$showForm = FALSE;
					$mBus->notification(_MSG_ERROR, 'Le concours #'.$contestID.' ne semble pas exister.');
				}
				break;
		}
		
		if ( $goback )
		{
			header('Location:'.$_SERVER['HTTP_REFERER']);
			exit(0);
		}
	}
	else
	{
		$contest['position'] = 0;
		$contest['title'] = NULL;
		$contest['text'] = NULL;
		$contest['url'] = NULL;
		$contest['url_alt'] = NULL;
	}
	
	if ( isset($_POST['submitted']) )
	{
		check_post($_POST);
		
		$postError = 0;
		
		$contest['position'] = get_value('position', 'p', 'uint');
		
		if ( !$contest['title'] = get_value('title', 'p', 'string') )
		{
			$postError++;
			$mBus->notification(_MSG_ERROR, 'Il faut un titre au concours.');
		}
		
		$contest['text'] = get_value('text', 'p', 'string');
		
		if ( !$contest['url'] = get_value('url', 'p', 'string') )
		{
			$postError++;
			$mBus->notification(_MSG_ERROR, 'Il manque l\'URL de jeuxconcours.be.');
		}
		
		if ( !$contest['url_alt'] = get_value('url_alt', 'p', 'string') )
		{
			$postError++;
			$mBus->notification(_MSG_ERROR, 'Il manque l\'URL de jeux-concours.be.');
		}
		
		if ( !$postError )
		{
			$showForm = FALSE;
			
			if ( $contestID )
				$sql = 'UPDATE `jc_contests` SET `position` = '.$contest['position'].', `title` = \''.addslashes($contest['title']).'\', `text` = \''.addslashes($contest['text']).'\', `url` = \''.addslashes($contest['url']).'\', `url_alt` = \''.addslashes($contest['url_alt']).'\' WHERE `id` = '.$contestID.' LIMIT 1;';
			else
				$sql = 'INSERT INTO `jc_contests` (`position`, `title`, `text`, `url`, `url_alt`) VALUES ('.$contest['position'].', \''.addslashes($contest['title']).'\', \''.addslashes($contest['text']).'\', \''.addslashes($contest['url']).'\', \''.addslashes($contest['url_alt']).'\');';
			
			if ( $db->execute($sql) )
				$mBus->notification(_MSG_SUCCESS, 'concours enregistré !');
			
			$HTMLBuffer .= "\t\t\t\t".'<p class="cmd"><a href="'._SITE_URL.'admin_contests_list.php" title="Affiche les concours">Liste des concours</a></p>'."\n";
			$HTMLBuffer .= "\t\t\t\t".'<p class="cmd"><a href="'._SITE_URL.'admin_contests_tools.php" title="Ajoute un nouveau concours">Ajouter un concours</a></p>'."\n";
		}
	}
	
	/* HTML document construction. */
	$page = new stdhtml($mBus, 'Jeuxconcours', 'admin');
	if ( $showForm )
	{
		$form = new stdform ();
		$form->set_title( $contestID ? 'Edition du concours #'.$contestID : 'Ajout d\'un concours' );
		
		if ( $tmp = $mBus->get_boxed_notifications() )
		{
			$form->add_html($tmp);
			$mBus->flush_notifications();
		}
		
		$form->add_text_field('title', 'Titre du concours', $contest['title']);
		$form->set_field_style('width:400px');
		$form->add_textarea_field('text', 'Texte de la case jaune', $contest['text']);
		$form->set_field_style('width:400px;height:150px');
		
		$form->add_text_field('position', 'Position dans la liste', $contest['position']);
		
		$form->add_text_field('url', 'Lien pour Jeuxconcours.be', $contest['url']);
		$form->set_field_style('width:600px');
		$form->add_text_field('url_alt', 'Lien pour Jeux-concours.be', $contest['url_alt']);
		$form->set_field_style('width:600px');
		
		if ( $contestID )
			$form->add_hidden_field('id', $contestID);
		$page->store($form->get_html(), 'content');
	}
	$page->display_notifications('content');
	if ( $HTMLBuffer )
		$page->store($HTMLBuffer, 'content');
	$page->store_module('menu', 'menu');
	echo $page->get_html();
	
?>
