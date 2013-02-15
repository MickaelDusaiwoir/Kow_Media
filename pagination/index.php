<?php
	
	header('content-type: text/html; charset=utf-8');
	
	// block entré nb par page cPage

	if(isset($_GET['p']) && $_GET['p'] > 0) {
		$cPage = $_GET['p'];
	} else {
		$cPage = 1;
	}
	
	$nbPage = 0;
	$nbParPage = 25;
	$pagination = null;
	$table = null;
	
	
	define('DSN', 'mysql:host=localhost;dbname=konkours'); 
	define('USER', 'root');
	define('PASS', 'stugdb');
	$options = array (
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	);

	try {
		$connex = new PDO(DSN, USER, PASS, $options);
		$connex->query('SET CHARACTER SET UTF8');
		$connex->query('SET NAMES UTF8');
	
		$req = 
		'SELECT COUNT(1) FROM `kk_users` '.
		'JOIN `kk_users_profiles` ON `kk_users`.`id` = `kk_users_profiles`.`user_id`;';
	
		$result = $connex->query($req);
		
		$userCount = $result->fetchColumn(0);
		
		if($userCount) {
		
			$nbPage = ceil($userCount/$nbParPage);
			
			if($cPage > $nbPage) {
				$cPage = $nbPage;
			}

			$start = $cPage - 5;
			if($start < 1) $start = 1;
			
			$end = $cPage + 5;
			if($end > $nbPage) $end = $nbPage;
			
			if($cPage > 1) 
				$pagination .= '<a href="index.php?p='.($cPage - 1).'">Précédent</a> - ';
			
			if($start > 1) $pagination.= ' ... ';
			for ($i = $start; $i<= $end; $i++){
			
				if($cPage == $i) {
					$pagination .= '<span>'.$i.'</span> - ';
				} else {
					$pagination .= '<a href="index.php?p='.$i.'">'.$i.'</a> - ';
				}
			}
			if($end < $nbPage) $pagination.= ' ... ';
			if($cPage < $nbPage){
				$pagination .= '<a href="index.php?p='.($cPage + 1).'">suivant</a>';
			}
			
			$req = 
				'SELECT `kk_users`.email, `kk_users`.`name`, `kk_users_profiles`.`lastname`, `kk_users_profiles`.`firstname` FROM `kk_users` '.
				'JOIN `kk_users_profiles` ON `kk_users`.`id` = `kk_users_profiles`.`user_id` '.
				'LIMIT '.( ($cPage -1) * $nbParPage ).' , '. $nbParPage .';';

			$result = $connex->query($req);
			$data = $result->fetchAll();
			
			if($data) {
			
				$table .= '
					<table>
						<thead>
							<tr>
								<th>
									Nom
								</th>
								<th>
									Prénom
								</th>
								<th>
									Pseudo
								</th>
								<th>
									Email
								</th>
							</tr>
						</thead>
						<tbody>
				';
				
				foreach( $data as $donnee ) {
				
					$table .= '
						<tr>
							<td>'.
								$donnee['lastname']
							.'</td>
							<td>'.
								$donnee['firstname']
							.'</td>
							<td>'.
								$donnee['name']
							.'</td>
							<td>'.
								$donnee['email']
							.'</td>
						</tr>
					';				
				}
				
				$table .= '
						</tbody>
					</table>
				';
			
			} else {
				echo 'Aucune donnée';
			}			
		
		} else {
			
			echo 'Aucun utilisateur trouvé';
			
		}	
		
	}  catch (PDOException $e) {
		die($e->getMessage());
	}
	
	echo $pagination;
	echo $table;
	echo $pagination;
	
	
?>