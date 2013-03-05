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
	$prize = array();
	
	$contestID = get_value('cid', 'gp', 'uint');
	
	if ( !$contestID )
	{
		header('Location:'._SITE_URL.'admin_contests_list.php');
		exit(0);
	}
	
	/* Edition ou ajout */
	if ( $prizeID = get_value('id', 'gp', 'uint') )
	{
		$goback = FALSE;
		
		switch ( get_value('action', 'g', 'string') )
		{
			case 'delete' :
				$showForm = FALSE;
				if ( $db->execute('DELETE FROM `jc_prizes` WHERE `id` = '.$prizeID.' LIMIT 1;') )
				{
					header('Location:'.$_SERVER['HTTP_REFERER']);
					exit(0);
				}
				break;
				
			case 'load' :
			default :
				if ( $prize = $db->get_row('SELECT `position`, `title`, `screenshot_url`, `count`, `value` FROM `jc_prizes` WHERE `id` = '.$prizeID.' LIMIT 1;') )
				{
					$prize['position'] = intval($prize['position']);
					$prize['count'] = intval($prize['count']);
					$prize['value'] = intval($prize['value']);
				}
				else
				{
					$showForm = FALSE;
					$mBus->notification(_MSG_ERROR, 'Le cadeau #'.$prizeID.' ne semble pas exister.');
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
		$prize['position'] = 0;
		$prize['title'] = NULL;
		$prize['screenshot_url'] = NULL;
		$prize['count'] = 1;
		$prize['value'] = 0;
	}
	
	if ( isset($_POST['submitted']) )
	{
		check_post($_POST);
		
		$postError = 0;
		
		$prize['position'] = get_value('position', 'p', 'uint');
		
		if ( !$prize['title'] = get_value('title', 'p', 'string') )
		{
			$postError++;
			$mBus->notification(_MSG_ERROR, 'Il faut un titre au cadeau.');
		}
		
		if ( !$prize['screenshot_url'] = get_value('screenshot_url', 'p', 'string') )
		{
			$postError++;
			$mBus->notification(_MSG_ERROR, 'Il manque l\'URL du screenshot.');
		}
		
		if ( !$prize['count'] = get_value('count', 'p', 'uint') )
		{
			$postError++;
			$mBus->notification(_MSG_ERROR, 'Il doit y avoir au moins 1x le cadeau');
		}
		
		if ( !$prize['value'] = get_value('value', 'p', 'uint') )
		{
			$postError++;
			$mBus->notification(_MSG_ERROR, 'Il faut une valeur pour le cadeau');
		}
		
		if ( !$postError )
		{
			$showForm = FALSE;
			
			if ( $prizeID )
				$sql = 'UPDATE `jc_prizes` SET `position` = '.$prize['position'].', `title` = \''.addslashes($prize['title']).'\', `screenshot_url` = \''.addslashes($prize['screenshot_url']).'\', `count` = '.$prize['count'].', `value` = '.$prize['value'].' WHERE `id` = '.$prizeID.' LIMIT 1;';
			else
				$sql = 'INSERT INTO `jc_prizes` (`contest_id`, `position`, `title`, `screenshot_url`, `count`, `value`) VALUES ('.$contestID.', '.$prize['position'].', \''.addslashes($prize['title']).'\', \''.addslashes($prize['screenshot_url']).'\', '.$prize['count'].', '.$prize['value'].');';
			
			if ( $db->execute($sql) )
				$mBus->notification(_MSG_SUCCESS, 'Cadeau enregistré !');
			
			$HTMLBuffer .= "\t\t\t\t".'<p class="cmd"><a href="'._SITE_URL.'admin_prizes_list.php?id='.$contestID.'" title="Affiche les concours">Liste des cadeaux du concours #'.$contestID.'</a></p>'."\n";
			$HTMLBuffer .= "\t\t\t\t".'<p class="cmd"><a href="'._SITE_URL.'admin_prizes_tools.php?cid='.$contestID.'" title="Ajoute un nouveau concours">Ajouter un autre cadeau pour le concours #'.$contestID.'</a></p>'."\n";
		}
	}
	
	/* HTML document construction. */
	$page = new stdhtml($mBus, 'Jeuxconcours', 'admin');
	if ( $showForm )
	{
		$form = new stdform ();
		$form->set_title( ($prizeID ? 'Edition du cadeau #'.$prizeID : 'Ajout d\'un cadeau').' pour le concours #'.$contestID );
		
		if ( $tmp = $mBus->get_boxed_notifications() )
		{
			$form->add_html($tmp);
			$mBus->flush_notifications();
		}
		
		$form->add_text_field('title', 'Titre du prix', $prize['title']);
		$form->set_field_style('width:200px');
		
		$form->add_text_field('screenshot_url', 'Lien de la capture', $prize['screenshot_url']);
		$form->set_field_style('width:400px');
		
		$form->add_text_field('count', 'Nombre de cadeaux', $prize['count']);
		$form->set_field_style('width:200px');
		
		$form->add_text_field('value', 'Valeur du cadeau', $prize['value']);
		$form->set_field_style('width:200px');
		
		$form->add_text_field('position', 'Position dans la liste', $prize['position']);
		
		$form->add_hidden_field('cid', $contestID);
		if ( $prizeID )
			$form->add_hidden_field('id', $prizeID);
		$page->store($form->get_html(), 'content');
	}
	$page->display_notifications('content');
	if ( $HTMLBuffer )
		$page->store($HTMLBuffer, 'content');
	$page->store_module('menu', 'menu');
	echo $page->get_html();
	
?>
