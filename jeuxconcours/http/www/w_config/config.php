<?php
	
	/* jeuxconcours.be - config file */
	
	define('_SITE_PATH', '/home/crazy/sites/jeuxconcours/http/www/');
	define('_SITE_URL', 'http://local.jeuxconcours.be/');
	
	define('_SAVE_LOG', 1);
	define('_LOG_FILE', _SITE_PATH.'w_log/log.txt'); 
	define('_WARN_MSG', 'L\'éxécution de la page a rencontré un erreur et ne peut pas continuer, veuillez réessayer plus tard. Si l\'erreur persiste, contactez l\'administrateur du site explicant le problème rencontré, merci de votre compréhension.');
	
	define('_DB_HOST', 'localhost');
	define('_DB_USER', 'jeuxconcours');
	define('_DB_PASS', 'pppppp');
	define('_DB_NAME', 'jeuxconcours');
	define('_DB_TYPE', 'mysqli');
	
	define('USER_DEBUG', FALSE);
	define('USER_TABLE', 'jc_users');
	define('USER_AUTHKEY_DAYLIFE', 15);
	
	define('STDHTML_MODULES_PATH', _SITE_PATH.'r_modules/');
	
	define('ALT_SITE', TRUE);
	
	$GLOBALS['_debug'] = TRUE;
	
?>
