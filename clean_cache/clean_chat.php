<?php 

	chdir('/home/crazy/web/Stigmatix/invocato/http/www/');
	
	include('r_config/boot.php');

	function delete_file ($file) 
	{
		if ( file_exists($file) && unlink($file) ) 
			return true;
		
		return false;
		
	}

	function folderFiles ($folder) 
	{
		if ( is_dir($folder) ) 
		{
			if ($entries = glob($folder."*")) 
			{
				foreach ( $entries as $entry) 
				{
					if ( !is_writable($entry) )
						return false;
					
					if ( is_file($entry) ) 
					{
						if ( !unlink($entry) )
							return false;
					} 
					elseif ( is_dir($entry) ) 
					{
						if ( !folderFiles($entry.'/') )
							return false;						
					}					
				}
			}

			if ( !rmdir($folder) )
				return false;
		} 
		else 
		{
			return false;
		}
		
		return true;		
	}
	
	$dir_cache = Config::get('site_path'). 'r_applications/chat/data/private/';

	if ( is_dir($dir_cache) )
	{
		if ( is_writable($dir_cache) )
		{
			$files = array(
				$dir_cache.'filemtime.test',
				$dir_cache.'filemtime2.test'
			);
			
			foreach ( $files as $file )
			{
				if ( file_exists($file) )
				{
					if ( !delete_file($file) )
						echo 'ERROR : removing "'.$file.'" failed '."\n";
				}
			}
			
			$dirs = array(
				$dir_cache.'cache/',
				$dir_cache.'chat/',
				$dir_cache.'logs/'
			);
			
			foreach ( $dirs as $dir )
			{
				if ( is_dir($dir) )
				{
					if ( !folderFiles($dir) )
						echo 'ERROR : removing "'.$dir.'" folder failed '."\n";
				}
			}
		}
		else
		{
			echo $dir_cache.' n\'est pas disponible en écriture !'."\n";
		}
	}
	else
	{
		echo $dir_cache.' n\'existe pas !'."\n";
	}
	
