<?php
	
	require_once('./w_config/config.php');
	require_once('./r_includes/class.mbus.php');
	require_once('./r_includes/class.dbaccess.php');
	require_once('./r_includes/class.stdhtml.php');
	require_once('./r_includes/class.stdform.php');
	require_once('./r_includes/class.stduser.php');
	require_once('./r_includes/functions.main.php');
	
	/* Init message bus system */
	$mBus = new mbus();
	/* Init database link and tools */
	$db = new dbaccess($mBus);	
	/* Init current user */
	$user = new stduser($mBus, $db);

	$HTMLBuffer = NULL;
	switch ( get_value('mode', 'gp', 'string', 'logout') )
	{
		case 'login' :
			$showForm = TRUE;
			$username = NULL;
			$password = NULL;
			$from = NULL;
			
			if ( isset($_POST['gw_submitted']) )
			{
				$postErrors = 0;
				
				if ( !$username = get_value('login', 'p', 'string') )
				{
					$postErrors++;
					$mBus->notification(_MSG_ERROR, 'L\'identifiant est vide !');
				}

				if ( !$password = get_value('password', 'p', 'string') )
				{
					$postErrors++;
					$mBus->notification(_MSG_ERROR, 'Il manque le mot de passe !');
				}

				$from = get_value('from', 'p', 'string');
				
				if ( !$postErrors )
				{
					
					if ( $user->login($username, $password) )
					{
						$showForm = FALSE;
						if ( $from )
						{
							header('Location:'.$from);
							exit(0);
						}
					}
				}
			}

			if ( $showForm )
			{
				$form = new stdform('gw');
				$form->add_text_field('login', 'Identifiant', $username);
				$form->add_password_field('password', 'Mot de passe', $password);
				$form->add_hidden_field('mode', 'login');
				$form->add_hidden_field('from', $from);
				$HTMLBuffer .= $form->get_html();
			}
			break;
			
		case 'logout' :
			break;

		default :
			/* Params error */
			break;
	}
	
	$page = new stdhtml($mBus, 'Gateway');
	$page->store($HTMLBuffer, 'content');
	$page->store_module('menu', 'menu');
	$page->display_notifications();
	echo $page->get_html();
	
?>
