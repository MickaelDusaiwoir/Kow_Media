<?php 

	function checkMail($email) {

		$email = trim(strtolower($email));
		$tmp = explode('@', $email);

		if(count($tmp) == 2) {
			
			$specialChar = '[a-zA-Z0-9 \. _\#\^\?!%\$&\\`\{\(|\}\)"-';
			$regex ='#^('.$specialChar.']+)@('.$specialChar.']+)$#'; 

			if(preg_match($regex, $email, $result)) {}
				return $email;	
			}
		}
		
		return null;
	}
	
	$email = '1tst@hotmail.com';
	
	if(checkMail($email))
		echo 'Valide';	
	else 
		echo 'Invalide mail';

?>

