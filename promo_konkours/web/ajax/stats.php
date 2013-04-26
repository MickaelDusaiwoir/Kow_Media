<?php 
	
	// on inclut les informations relatives à la base de données.
	include('../../application/config/database.php');

	// On vérifie s'il y a un cookie portant le nom de stats visitor, s'il y en a un on le décode.
	// Sinon on retourne un message d'erreur et on stop le script.
	if ( isset($_COOKIE['stats_visitor']) )
	{
		$userData = json_decode($_COOKIE['stats_visitor']);
	}
	else
	{
		echo 'ERROR:NO_COOKIE';

		exit(1);
	}

	// On vérifie que l'on récupère bien l'id du concours et que celui-ci est un nombre.
	// Sinon on retourne un message d'erreur et on stop le script.
	if ( !$contestID = isset($_POST['contest_id']) ? intval($_POST['contest_id']) : 0 )
	{
		echo 'ERROR:NO_CONTEST_ID';

		exit(0);
	}

	if (  isset($_POST['view']) )
	{
		if ( is_numeric($_POST['view']) )
		{
			$view = $_POST['view'];
		}
		else
		{
			echo 'ERROR:VIEW IS NOT A NUMBER';

			exit(0);
		}
	}
	else
	{
		echo 'ERROR:NO VIEW';

		exit(0);
	}

	if (  isset($_POST['css']) )
	{
		if ( is_numeric($_POST['css']) )
		{
			$css = $_POST['css'];
		}
		else
		{
			echo 'ERROR:CSS IS NOT A NUMBER';

			exit(0);
		}
	}
	else
	{
		echo 'ERROR:NO CSS';

		exit(0);
	}


	if ( isset($_COOKIE['subid']) )
		$subid = $_COOKIE['subid'];
	else
		$subid = '';

	$options = array (
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	);

	try 
	{
		$connex = new PDO('mysql:host='.$db['default']['hostname'].';dbname='.$db['default']['database'], $db['default']['username'], $db['default']['password'], $options);
		$connex->query('SET CHARACTER SET UTF8');
		$connex->query('SET NAMES UTF8');

		// On ajout dans la base de donnée le concours qui vient d'être regarder en le liant à l'utilisateur par son id.
		$req = 
			"INSERT INTO `stat_contest_click`(`visitor_id`, `ip`, `contest_id`, `date`, `view`, `css`, `subid`) ".
			"VALUES(".$userData->visitor_id.", ".ip2long($_SERVER['REMOTE_ADDR']).", ".$contestID.", ".time().", ".$view.", ".$css.", '".$subid."');";
		
		// On vérifie que la requete c'est bien passé et que aucune erreur n'est survenue. 
		// S'il n'y a pas d'erreur alors ont envois un message qui confirme l'ajout.
		// S'il y a une erreur on envoie un message d'erreur.
		if ( $connex->query($req) !== false )
			echo "SUCCESS: Click on a contest has count \n";
		else
			echo 'ERROR:QUERY_FAILED';

		// on initialise ou on met à jour le nombre clic qu'a fait l'utilisateur sur un concours.
		$req = 
			"INSERT INTO `stats_visitors_click`(`visitor_id`,`date`,`click_count`)".
			" VALUES (".$userData->visitor_id.",'".date('d-m-Y', time())."', 1)".
			" ON DUPLICATE KEY UPDATE click_count = click_count+1 ;";

		// On vérifie que la requete c'est bien passé et que aucune erreur n'est survenue. 
		// S'il n'y a pas d'erreur alors ont envois un message qui confirme l'ajout.
		// S'il y a une erreur on envoie un message d'erreur.
		if ( $connex->query($req) !== false )
			echo "SUCCESS: Visitor click has count \n";
		else
			echo 'ERROR:QUERY_FAILED';
	}
	catch (PDOException $e) 
	{
		die($e->getMessage());

		echo 'ERROR:NO_DATABASE';
	}
