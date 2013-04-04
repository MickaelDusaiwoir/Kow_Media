<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
<?php

	if ( $_GET['url'] ) :

		$url = base64_decode($_GET['url']);

?>
		<form action="<?= $url ?>" method="post" id="referer">
			<input type="hidden" />
		</form>
<?php
	else :
?>
		<p>
			Une erreur est survenue, la page n'a pu être trouvée.
		</p>
<?php
	endif;
?>
		<script src="../js/hideRef.js"></script>  
	</body>		
</html>