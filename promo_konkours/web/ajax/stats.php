<?php 

	include('../../application/config/database.php');

	if ( isset($_COOKIE['stats_visitor']) )
	{
		$userData = json_decode($_COOKIE['stats_visitor']);
	}
	else
	{
		echo 'ERROR:NO_COOKIE';

		exit(1);
	}

	if ( !$contestID = isset($_POST['contest_id']) ? intval($_POST['contest_id']) : 0 )
	{
		echo 'ERROR:NO_CONTEST_ID';

		exit(0);
	}

	$options = array (
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	);

	try 
	{
		$connex = new PDO('mysql:host='.$db['default']['hostname'].';dbname='.$db['default']['database'], $db['default']['username'], $db['default']['password'], $options);
		$connex->query('SET CHARACTER SET UTF8');
		$connex->query('SET NAMES UTF8');

		$req = 
			"INSERT INTO `stats`(`visitor_id`, `ip`, `contest_id`, `date`) ".
			"VALUES(".$userData->visitor_id.", ".ip2long($_SERVER['REMOTE_ADDR']).", ".$contestID.", ".time().");";
		
		if ( $connex->query($req) !== false )
			echo 'SUCCESS';
		else
			echo 'ERROR:QUERY_FAILED';
	}
	catch (PDOException $e) 
	{
		die($e->getMessage());

		echo 'ERROR:NO_DATABASE';
	}
