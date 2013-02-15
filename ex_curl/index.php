<?php 


function getdata ($input) {

	$data = array('title' => null, 'image' => array() );
	
	$startTitle = explode('<h3>', $input);
	$tmp = explode('</h3>', $startTitle[1]);
	$data['title'] = strip_tags($tmp[0]);
	
	$result = array();
	$tmp = array();
	// #<img src="([^"]+)" alt="([^"]+)" title="([^"]+)" data-src="([^"]+)" #
	if(preg_match('#<img src="([^"]+)" alt="([^"]+)" title="([^"]+)" data-src="([^"]+)"#', $input, $result)){

		$tmp = getimagesize($result[4]);
		
		$data['image']['src'] = $result[1];
		$data['image']['alt'] = $result[2];
		$data['image']['title'] = $result[3];
		$data['image']['data-src'] = $result[4];
		$data['image']['width'] = $tmp[0];
		$data['image']['height'] = $tmp[1];
		
	}

	return $data;
}


if($ch = curl_init()) { 
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, 'http://www.clubic.com');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	$html = curl_exec($ch);
	curl_close($ch);
	
	if($html) {
	
		$start = strpos($html, '<ul id="news">');
	
		if( $start === false) {
		
			echo 'Chaine non trouvée';	
		
		} else {
		
			$end = strpos($html, '</ul>', $start);
			
			if( $end === false) {

				echo 'Fin non trouvée';	

			}else{

				if($block = substr($html, $start, $end - $start)) {	
					
					$results = array();
					
					$li = explode('</li>', $block);
					
					$count = count($li) - 1;
					
					for($i = 0; $i < $count; $i++) {
						
						$results[] = getData($li[$i]);
						
					}
					
					foreach( $results as $result ){
						echo '<p>'.$result['title'].'</p>';
						
						if( $result['image']) {
							echo '<img src="'.$result['image']['data-src'].'" alt="'.$result['image']['alt'].'" title="'.$result['image']['title'].'" data-src="'.$result['image']['data-src'].'" width="'.$result['image']['width'].'" height="'.$result['image']['height'].'" />';
						}
						else {
							echo "Pas d'image disponible";
						}						
					}			
				} else {
				
					echo 'Aucune chaîne de caractère';
				}				
			}			
		}	
	} else {
		echo 'aucune donnée reçue';
	}	
	
} else {
	echo 'Curl inutilisable';
}


	
			
?>