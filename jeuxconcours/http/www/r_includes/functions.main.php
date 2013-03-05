<?php
	
	/*
		Main utility functions - v2.0 (19/03/2010)
		Author : LondNoir (londnoir@sdmedia.be)
		
		Caching system
		
		Main functions used in my scripts
		
	*/
	
	function raw_to_html ($textPlain)
	{
		$textHTML = str_replace("\n", '<br />', $textPlain);
		
		$textHTML = preg_replace('#\[(b|u|i|/b|/u|/i)\]#', '<$1>', $textHTML);
		
		return $textHTML;
	}
	
	/* This function will build an entire path. */
	function build_path ($path, $permission = '0755')
	{
		$pathTest = NULL;
		$dirs = explode('/', $path);
		
		foreach ($dirs as $dir)
		{
			$pathTest .= $dir.'/';
			if ( !is_dir($pathTest) )
			{
				if (!mkdir($pathTest, $permission))
					return FALSE;
				
				chmod($pathTest, $permission);
			}
		}
		
		return TRUE;
	}
	
	/* This function will summarize a text with style */
	if ( PHP_VERSION_ID >= 60000 )
	{
		function sum ($buffer, $limit = 50, $disableFX = FALSE)
		{
			$buffer = html_entity_decode($buffer, ENT_QUOTES);
			
			if ( strlen($buffer) > $limit )
			{
		    	$buffer = substr($buffer, 0, $limit);
		    	
		    	if ( !$disableFX )
		    		$buffer .= '...';
			}
			
			 return htmlspecialchars($buffer, ENT_COMPAT);
		}
	}
	else
	{
		function sum ($buffer, $limit = 50, $disableFX = FALSE)
		{
			/* NOTE : this function is for PHP5.
			UTF-8 strings length are handled as ISO chars, so 'é' count for 2 chars.
			This function is intented to cut only human visible chars. */
		
			$buffer = html_entity_decode($buffer, ENT_QUOTES, 'UTF-8');
			$buffer = utf8_decode($buffer);
		
			if ( strlen($buffer) > $limit )
			{
		    	$buffer = substr($buffer, 0, $limit);
		    	
		    	if ( !$disableFX )
		    		$buffer .= '...';
			}
		
			$buffer = utf8_encode($buffer);
		
		    return htmlspecialchars($buffer, ENT_COMPAT, 'UTF-8');
		}
	}
	
	/* This function return FALSE on
	detected javascript in buffer */
	function is_js_free ($buffer)
	{
		if ( !is_string($buffer) )
			return TRUE;
		
		/* Simplify for research */
		$buffer = strtolower($buffer);
		
		if ( strstr($buffer, '<script') )
			return FALSE;
		elseif ( strstr($buffer, 'script/>') )
			return FALSE;
		elseif ( strstr($buffer, 'type="text/javascript"') )
			return FALSE;
		elseif ( strstr($buffer, 'language="javascript"') )
			return FALSE;
		else
			return TRUE;
	}
	
	function get_value ($index, $order, $type, $default = FALSE)
	{
		if ( !$index )
			return NULL;
		
		$count = strlen($order);
		$value = FALSE;
		
		/* diff between string and array values */
		switch ( $type )
		{
			case 'array_int' :
			case 'array_uint' :
			case 'array_string' :
			case 'array_string_public' :
				for ( $i = 0; $i < $count; $i++ )
				{
					switch ($order[$i])
					{
						case 'g' :
							$value = ( isset($_GET[$index]) && is_array($_GET[$index]) && count($_GET[$index]) ) ? $_GET[$index] : FALSE;
							break;
					
						case 'p' :
							$value = ( isset($_POST[$index]) && is_array($_POST[$index]) && count($_POST[$index]) ) ? $_POST[$index] : FALSE;
							break;
					
						case 'c' :
							$value = ( isset($_COOKIE[$index]) && is_array($_COOKIE[$index]) && count($_COOKIE[$index]) ) ? $_COOKIE[$index] : FALSE;
							break;
					
						case 's' :
							$value = ( isset($_SESSION[$index]) && is_array($_SESSION[$index]) && count($_SESSION[$index]) ) ? $_SESSION[$index] : false;
							break;
					
						default :
							return 0;
							break;
					}
			
					if ( $value !== FALSE )
						break;
				}
				break;
				
			default :
				for ( $i = 0; $i < $count; $i++ )
				{
					switch ($order[$i])
					{
						case 'g' :
							$value = ( isset($_GET[$index]) && is_string($_GET[$index])  && strlen($_GET[$index]) ) ? $_GET[$index] : FALSE;
							break;
					
						case 'p' :
							$value = ( isset($_POST[$index]) && is_string($_POST[$index]) && strlen($_POST[$index]) ) ? $_POST[$index] : FALSE;
							break;
					
						case 'c' :
							$value = ( isset($_COOKIE[$index]) && is_string($_COOKIE[$index]) && strlen($_COOKIE[$index]) ) ? $_COOKIE[$index] : FALSE;
							break;
					
						case 's' :
							$value = isset($_SESSION[$index]) ? $_COOKIE[$index] : FALSE;
							break;
					
						default :
							return 0;
							break;
					}
			
					if ( $value !== FALSE )
						break;
				}
				break;
		}
		
		if ( $value === FALSE )
		{
			if ( $default === FALSE )
			{
				switch ( $type )
				{
					case 'string_b64' :
					case 'string_md5' :
					case 'string_trim' :
					case 'string_public' :
					case 'string' :
						return NULL;
						break;
						
					case 'double' :
					case 'float' :
						return 0.0;
						break;
						
					case 'array_int' :
					case 'array_uint' :
					case 'array_string' :
					case 'array_string_public' :
						return array();
						break;
						
					case 'int' :
					case 'uint' :
					case 'bool' :
					default :
						return 0;
						break;
				}
			}
			else
			{
				/* return default set by the user */
				return $default;
			}
		}
		/* Process return type */
		else
		{
			switch ( $type )
			{
				case 'string' :
					return strval($value);
					break;
					
				case 'uint' :
					return max(0, intval($value));
					break;
					
				case 'int' :
					return intval($value);
					break;
					
				case 'bool' :
					return $value ? 1 : 0;
					break;
					
				case 'string_b64' :
					return base64_decode($value);
					break;
					
				case 'string_md5' :
					return md5($value);
					break;
					
				case 'string_trim' :
					return trim($value);
					break;
					
				case 'string_public' :
					if ( is_js_free($value) )
						return $value;
					else
						return NULL;
					break;
					
				case 'double' :
					return doubleval($value);
					break;
					
				case 'float' :
					return floatval($value);
					break;
					
				case 'array_int' :
					foreach ($value as $k => $v)
						$value[$k] = intval($v);
					return $value;
					break;
					
				case 'array_uint' :
					foreach ($value as $k => $v)
						$value[$k] = max(0, intval($v));
					return $value;
					break;
					
				case 'array_string' :
					return $value;
					break;
					
				case 'array_string_public' :
					foreach ($value as $k => $v)
						if ( !is_js_free($v) )
							$value[$k] = NULL;
					return $value;
					break;
					
				default :
					return $value;
					break;
			}
		}
	}
	
	/* This is a cool function wich take any text
	and convert it to a nice URL friendly string */
	function str_to_url ($string)
	{
		$input = array();
		$output = array();
		
		/* Remove all HTML entities from the string */
		$string = html_entity_decode($string, ENT_QUOTES, 'utf-8');
		
		/* Lower case */
		$string = mb_strtolower($string, mb_detect_encoding($string));
		
		/* Replaced  chars */
		$input[] = array('á', 'à', 'ä', 'â');
		$output[] = 'a';
		$input[] = array('é', 'è', 'ë', 'ê');
		$output[] = 'e';
		$input[] = array('í', 'ì', 'ï', 'î');
		$output[] = 'i';
		$input[] = array('ó','ò','ö','ô');
		$output[] = 'o';
		$input[] = array('ú', 'ù', 'ü', 'û');
		$output[] = 'u';
		$input[] = array('ý', 'ÿ');
		$output[] = 'y';
		$input[] = 'ç';
		$output[] = 'c';
		$input[] = 'œ';
		$output[] = 'oe';
		$input[] = '$';
		$output[] = 'dollars';
		$input[] = '€';
		$output[] = 'euros';
		
		/* Replacement */
		for ( $i = 0; $i < count($input); $i++ )
			$string = str_replace($input[$i], $output[$i], $string);
		
		/* Replace all char wich is not alphanumeric by '-' char */
		$string = preg_replace('#([^A-Za-z0-9-]+)#', '-', $string);
		
		/* Remove undesirable '-' char */
		$string = preg_replace('#[-]{2,}#', '-', $string);
		$string = trim($string, '-');
		
		return $string;
	}
	
	function check_email_syntax ($string)
	{	
		if ( !$string || !is_string($string) )
			return FALSE;
		
		if ( !preg_match('#^[\w@._-]+$#', $string) )
			return FALSE;
		
		$testAt = explode('@', $string);
		if ( count($testAt) != 2 || !$testAt[1] )
			return FALSE;
		
		$testDot = explode('.', $testAt[1]);
		if ( count($testDot) == 1 || !$testDot[1] )
			return FALSE;
		
		if ( htmlspecialchars($string) != $string )
			return FALSE;
		
		return $string;
	}
	
	function is_addr_use_site_domain ($email)
	{
		$tmp = explode('@', $email);
		
		return ( end($tmp) === _SITE_DOMAIN );
	}
	
	function array_form_encode (array $cleanArray)
	{
		$encodedString = serialize($cleanArray);
		$encodedString = urlencode($encodedString);
		
		return $encodedString;
	}
	
	function array_form_decode ($encodedString)
	{
		$cleanArray = urldecode($encodedString);
		$cleanArray = unserialize($cleanArray);
		
		return $cleanArray;
	}
	
	function check_post (&$array)
	{
		/* Remove autoslashes */
		if ( get_magic_quotes_gpc() )
		{
			foreach ($array as $id => $cell)
			{
				/* Avoid second level array destruction */
				if ( is_string($cell) )
					$array[$id] = stripslashes($cell);
			}
		}
	}
	
?>
