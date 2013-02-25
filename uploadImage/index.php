<html>
	<head>
		<title>Upload image</title>
		<meta charset="utf-8">
	</head>
	<body>
		<form action="index.php" method="post" enctype="multipart/form-data">
			<fieldset>
				<label for="img">Image</label>
				<input type="file" name="img" id="img" />
				<input type="hidden" name="MAX_FILE_SIZE" value="2097152"> 
				<input type="submit" name="uploader" value="uploader" />
			</fieldset>
		</form>
	</body>
</html>

<?php
		

	if(isset($_FILES['img'])){
		
		if(!$_FILES['img']['error']){
		
			if($data = getimagesize($_FILES['img']["tmp_name"])) {
				
				$error = 0;
			
				$content_dir = "uploads/";

				$nom = 'f'. time() . rand(0,1000) . '.jpg';
				
				$taille = 250;
				
				switch($_FILES['img']['type']) {
				
					case 'image/jpeg':
						$image = imagecreatefromjpeg($_FILES['img']["tmp_name"]);
					break;
					
					case 'image/png':
						$image = imagecreatefrompng($_FILES['img']["tmp_name"]);
					break;
					
					default:
						echo 'Introduissez une image de type png ou jpg';
						$error ++;
					break;
				}				
				
				if($error == 0) {
					$thumbnail = imagecreatetruecolor($taille, $taille); 
					 
					if ($data[0] > $data[1]) { // paysage
					
						$position = ($data[0] - $data[1]) /2;
					
						imagecopyresampled($thumbnail, $image, 
							4, 4, // dst offsets
							$position, 0,  // src offsets
							$taille -8, $taille -8, // dst sizes
							$data[1], $data[1] // src sizes
						);						
					} else { // portrait
					
						$position = ($data[1] - $data[0]) /2;
					
						imagecopyresampled($thumbnail, $image, 
							0, 0, // dst offsets
							0, $position, // src offsets
							$taille, $taille, // dst sizes
							$data[0], $data[0] // src sizes
						);
						
					}
					
					$text = 'Hello World';
					$textColor = imagecolorallocate($thumbnail, 255, 255, 255);
					imagestring($thumbnail, 10, 8, $taille - 20, $text, $textColor); 		
					
					imagejpeg ($thumbnail, $content_dir.'min_'.$nom);				
					imagejpeg ($image, $content_dir.$nom);
					
					}
					
					echo '<img src="'.$content_dir.'min_'.$nom.'" />';
				}
			} else {
				echo 'Entrez uniquement une image';
			}
		} else {
			switch ($_FILES['img']['error']){ 
			
			   case 1: // UPLOAD_ERR_INI_SIZE     
			   echo"Le fichier dépasse la limite autorisée par le serveur (fichier php.ini) !";     
			   break;  
			   
			   case 2: // UPLOAD_ERR_FORM_SIZE     
			   echo "Le fichier dépasse la limite autorisée dans le formulaire HTML !"; 
			   break;  
			   
			   case 3: // UPLOAD_ERR_PARTIAL     
			   echo "L'envoi du fichier a été interrompu pendant le transfert !";     
			   break;  
			   
			   case 4: // UPLOAD_ERR_NO_FILE     
			   echo "Le fichier que vous avez envoyé a une taille nulle !"; 
			   break;     
			}     
		}
	} 
	

?>
