<?php

	function getPath ($size, $id)
	{
		if ( $size && $id )
		{
			$tmp = ceil( $id / 1000 ) * 1000;

			return '../web/uploads/'.$size.'/'.($tmp - 1000).'-'.$tmp.'/';	
		}

		return false;
	}

	function checkRepertory ($path) 
	{
		if ( $path )
		{
			if ( !($dirs = explode('/', $path)) )
				return false;
			
			$parent = $dirs[0] ? '' : '/';
			
			foreach ( $dirs as $currentdir )
			{
				// Elimine les "//", "/" de début et de fin
				if ( !$currentdir )
					continue;
				
				if ( is_dir($parent.'/'.$currentdir) || $currentdir === '.' || $currentdir === '..' )
				{
					$parent .= $currentdir.'/';
					
					continue;
				}
				
				if ( $parent && !is_writable($parent) )
					return false;
				
				$parent .= $currentdir.'/';
				
				mkdir($parent, 0755);
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}

	/* 

	function checkRepertoryb ($path) 
	{
		if( $path )
		{
			$tmp = explode( '/', $path );
			$repertory = '';
			$parent = '';

			for ( $i = 0; $i < count($tmp) - 1; $i++ )
			{
				if ( $tmp[$i] !== '.' && $tmp[$i] !== '..' )
				{
					$repertory .= $tmp[$i].'/';

					if ( !is_dir($repertory) )
					{
						if ( is_writable($parent) )
						{
							mkdir($repertory, 0777);
						}
						else
						{
							var_dump('ici' + $tmp[$i]);
							return false;
						}
					}
					else
					{
						$parent = $repertory;
					}
				}
				else
				{
					$repertory .= $tmp[$i].'/';
				}

				var_dump($repertory);

				if ( count($tmp) -2 == $i )
					return TRUE;
			}
		}
		else
		{
			var_dump('Oh yeah');
			return false;
		}
	}


	*/


?>
