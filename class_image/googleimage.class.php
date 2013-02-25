<?php
	
 /*
  * r_lib/googleimage.class.php
  * This file is part of PHP-LTK 
  *
  * Copyright (C) 2012 - LondNoir <londnoir@sdmedia.be>
  *
  * PHP-LTK is free software; you can redistribute it and/or
  * modify it under the terms of the GNU Lesser General Public
  * License as published by the Free Software Foundation; either
  * version 2.1 of the License, or (at your option) any later version.
  * 
  * PHP-LTK is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  * Lesser General Public License for more details.
  * 
  * You should have received a copy of the GNU Lesser General Public
  * License along with this library; if not, write to the Free Software
  * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  */
	
	/**
	* @file googleimage.class.php
	* @author LondNoir (londnoir@sdmedia.be)
	* @version 1.2 (24/04/2012)
	* @brief GoogleImage class file.  */
	
	/**
	* @class GoogleImage
	* @author LondNoir (londnoir@sdmedia.be)
	* @version 1.2 (24/04/2012)
	* @brief Cette classe permet de télécharger les images des résultats de "Google Image".
	* @details Il suffit de passer les mots à rechercher et la classe retourne un tableau associatif avec les liens vers les images.
	*/
	class GoogleImage
	{
		/** @brief Toutes les tailles. */
		const SIZE_ALL = 0;
		/** @brief Tailles de type icône de bureau. */
		const SIZE_ICON = 1;
		/** @brief Tailles moyennes. */
		const SIZE_MEDIUM = 2;
		/** @brief Tailles larges. */
		const SIZE_LARGE = 3;
		
		static private $forbiddenNames = array(
			'photobucket',
			'imageshack',
			'flickr'
		);
		
		// Search parameters
		private $size = null;
		private $safeui = null;
		private $domain = 'images.google.be';
		private $lang = 'fr';
		private $coding = null;
		
		private $search = null;
		private $results = array();
		
		/** 
		* @brief Le constructeur.
		* @param $size Une des constantes suivantes :\n
		* GoogleImage::SIZE_ALL\n
		* GoogleImage::SIZE_ICON\n
		* GoogleImage::SIZE_MEDIUM\n
		* GoogleImage::SIZE_LARGE\n
		* @param $disableSafeSearch Un booléen pour désactive le mode "Safe Search" de Google.
		* @param $domain Une chaîne de caractères représentant le nom de domaine sur lequel effectuer la recherche.
		* @param $lang Une chaîne de caractères représentant la langue utilisée pour les recherches.
		* @param $coding Une chaîne de caractères représentant l'encodage utilisé pour les recherches.
		*/
		function __construct ($size = self::SIZE_ALL, $disableSafeSearch = false, $domain = null, $lang = null, $coding = null)
		{
			switch ( $size )
			{
				case self::SIZE_ICON :
					$this->size = 'imgo:0,isz:i';
					break;
					
				case self::SIZE_MEDIUM :
					$this->size = 'imgo:0,isz:m';
					break;
					
				case self::SIZE_LARGE :
					$this->size = 'imgo:0,isz:l';
					break;
			}
			
			$this->safeui = $disableSafeSearch ? 'off' : 'on';
			
			if ( $domain )
				$this->domain = $domain;
			
			if ( $lang )
				$this->lang = $lang;
			
			$this->coding = $coding ? $coding : 'UTF-8';
		}
		
		private function buildRequestURL ()
		{
			$url = 'http://'.$this->domain.'/search?q='.$this->search.'&tbm=isch';
			
			if ( $this->lang )
				$url .= '&hl='.$this->lang;
			
			if ( $this->coding )
				$url .= '&ie='.$this->coding;
			
			if ( $this->size )
				$url .= '&tbs='.$this->size;
			
			// on, off, images
			$url .= '&safe='.$this->safeui;
			
			return $url;
		}
		
		private function checkRequest ($request)
		{
			$request = str_replace(' ', '+', $request);
			$request = urlencode($request);
			
			return $request;
		}
		
		private function getContent ($url)
		{
			if ( $curl = curl_init() )
			{
				$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
				$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
				$header[] = "Cache-Control: max-age=0";
				$header[] = "Connection: keep-alive";
				$header[] = "Keep-Alive: 300";
				$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
				$header[] = "Accept-Language: en-us,en;q=0.5";
				$header[] = "Pragma: ";
				
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				
				$html = curl_exec($curl);
				curl_close($curl);
				
				return $html;
			}
			else
			{
				return null;
			}
		}
		
		/** 
		* @brief Débute une recherche sur google image.
		* @param $search Une chaîne de caractères représentant les termes recherchés.
		* @return Un nombre entier pour le nombre d'image trouvée ou false en cas de problème.
		*/
		public function search ($search)
		{
			$this->search = $this->checkRequest($search);
			
			if ( !$this->search )
			{
				trigger_error(__CLASS__.'::search(), try to set a google search without words or bad ones ...', E_USER_WARNING);
				
				return false;
			}
			
			$url = $this->buildRequestURL();
			
			if ( !($buffer = $this->getContent($url)) )
			{
				trigger_error(__CLASS__.'::search(), empty web page !', E_USER_WARNING);
				
				return false;
			}
			
			if ( true )
			{
				// HTML Version
				// Raw definitions of images
				$imagesDefs = array();
			
				$startOffset = 0;
				$endOffset = 0;
				$searching = true;
				
				while ( $searching )
				{
					// Search for the first TD html tag
					if ( ($startOffset = strpos($buffer, '<td', $endOffset)) !== false )
					{
						// Search for the end of this TD html tag
						if ( ($endOffset = strpos($buffer, '</td>', $startOffset)) !== false )
						{
							$endOffset += strlen('</td>');
							
							$line = substr($buffer, $startOffset, $endOffset - $startOffset);
							
							if ( strstr($line, 'imgres?imgurl') )
								$imagesDefs[] = $line;
						}
						else
						{
							trigger_error(__CLASS__.'::search(), HTML tag formation error !', E_USER_WARNING);
						
							$searching = false;
						}
					}
					else
					{
						$searching = false;
					}
				}
				
				if ( !$imagesDefs )
					return 0;
				
				foreach ( $imagesDefs as $definitions )
				{
					// Example = <td style="width:25%;word-wrap:break-word"><a href="/imgres?imgurl=http://lesbeautesdemontreal.files.wordpress.com/2010/07/montreal-saffiche_003.jpg&amp;imgrefurl=http://lesbeautesdemontreal.com/2010/07/31/montreal-saffiche/&amp;usg=__pyteoUpKb1mibdO4hJT0ncUtPD8=&amp;h=1588&amp;w=1055&amp;sz=603&amp;hl=fr&amp;start=3&amp;zoom=1&amp;tbnid=u4idkTITUVpEUM:&amp;tbnh=150&amp;tbnw=100&amp;ei=BonhT7fbN4myhAeM4bDQAw&amp;prev=/search%3Fq%3DUn%252Bs%25C3%25A9jour%252B%25C3%25A0%252BMontr%25C3%25A9al%26hl%3Dfr%26safe%3Doff%26ie%3DUTF-8%26tbs%3Disz:l%26tbm%3Disch&amp;itbs=1"><img height="150" width="100" src="http://t1.gstatic.com/images?q=tbn:ANd9GcSmUcqENRtOHvhqGO31jd9adt0WwX0Rzy0C6KPLe_AKYFaZdMyWHs9GgyE"></a><br>gagnez <b>un s�jour</b> au bord<br>1055 &times; 1588 - 603 ko&nbsp;-&nbsp;jpg<br><cite title="lesbeautesdemontreal.com">lesbeautesdemontreal.com</cite></td>
					$forbidden = false;
				
					foreach ( self::$forbiddenNames as $name )
					{
						if ( strstr($definitions, $name) !== false ) // Checking Copyright
						{
							$forbidden = true;
							break;
						}
					}
				
					if ( $forbidden )
						continue;
				
					$media = array(
						'thumb' => null,
						'thumb_width' => 0,
						'thumb_height' => 0,
						'url' => null,
						'width' => 0,
						'height' => 0
					);
					
					$matches = array();
					if ( !preg_match('#<img height="([0-9]+)" width="([0-9]+)" src="([^"]+)"#', $definitions, $matches) )
						continue;
				
					$media['thumb'] = $matches[3];
					$media['thumb_width'] = intval($matches[2]);
					$media['thumb_height'] = intval($matches[1]);
					
					$matches = array();
					if ( !preg_match('#/imgres\?imgurl=([^&]+)&amp;#', $definitions, $matches) )
						continue;
				
					$media['url'] = $matches[1];
				
					// Récupération des dimensions de l'image
					$matches = array();
					if ( !preg_match('#([0-9]+) &times; ([0-9]+) - ([0-9]+) ko#', $definitions, $matches) )
						continue;
				
					$media['width'] = intval($matches[1]);
					$media['height'] = intval($matches[2]);
				
					// On ajoute le resultat dans le tableau google
					$this->results[] = $media;
				}
			}
			else
			{
				// Javascript version
				// Get the javascript container off the buffer
				$matches = array();
				if ( !preg_match('#dyn\.setResults\(\[\[([^<]+)\]\]\);#', $buffer, $matches) )
					return 0;
			
				unset($buffer);
			
				// Convert JS definitions array to PHP real array.
				$imagesDefs = array();
				if ( $results = explode('],[', $matches[1]) )
				{
					unset($matches);
				
					$imageNum = 0;
				
					foreach ( $results as $result )
					{
						$len = strlen($result);
						$insideQuotes = false;
						$copy = false;
					
						$imagesDefs[$imageNum] = array();
					
						$infoNum = 0;
						for ( $chr = 0; $chr < $len; $chr++ )
						{
							$startCopyAfterThisChar = false;
						
							//echo  $result[$chr]."\n";
						
							switch ( $result[$chr] )
							{
								case '"' :
									$insideQuotes = $insideQuotes ? false : true;
								
									if ( $insideQuotes )
									{
										//echo 'QUOTES DETECTED : START COPYING...'."\n";
									
										$startCopyAfterThisChar = true;
									
										$infoNum++;
										$imagesDefs[$imageNum][$infoNum] = null;
									}
									else
									{
										//echo 'QUOTES DETECTED : STOP COPYING !'."\n";
									
										$copy = false;
									}
									break;
								
								case '[' :
									//echo '"[" DETECTED : START COPYING...'."\n";
								
									$startCopyAfterThisChar = true;
								
									$infoNum++;
									$imagesDefs[$imageNum][$infoNum] = null;
									break;
								
								case ']' :
									//echo '"]" DETECTED : STOP COPYING !'."\n";
								
									$copy = false;
									break;
								
								default :
									// just go on
									break;
							}
						
							if ( $copy )
								$imagesDefs[$imageNum][$infoNum] .= $result[$chr];
						
							if ( $startCopyAfterThisChar )
								$copy = true;
						}
					
						$imageNum++;
					}
				}
			
				if ( !$imagesDefs )
					return 0;
			
				foreach ( $imagesDefs as $definitions )
				{
					$forbidden = false;
				
					foreach ( self::$forbiddenNames as $name )
					{
						if ( strstr($definitions[4], $name) !== false ) // Checking Copyright
						{
							$forbidden = true;
							break;
						}
				
						if ( strstr($definitions[4], $name) !== false ) // Checking URL
						{
							$forbidden = true;
							break;
						}
					}
				
					if ( $forbidden )
						continue;
				
					// Récupération des dimensions de l'image
					$imageSize = array();
					if ( !preg_match('#^([0-9]+) &times; ([0-9]+) - ([0-9]+) ko$#', $definitions[10], $imageSize) )
						continue;
				
					$imageURL = array(); // \x3d
					if ( !preg_match('#^/imgres\?imgurl\\\x3d([^\\\]+)\\\x26#', $definitions[1], $imageURL) )
						continue;
				
					// On ajoute le resultat dans le tableau google
					$this->results[] = array(
						'thumb' => str_replace('\x3d', '=', $definitions[21]),
						'width' =>  intval($imageSize[1]),
						'height' => intval($imageSize[2]),
						'url' => $imageURL[1]
					);
				}
			}
			
			return count($this->results);
		}
		
		/** 
		* @brief Retourne le tableau d'images.
		* @return Un tableau associatif structuré de cette manière : array("thumb", "width", "height", "url")
		*/
		public function getResults ()
		{
			return $this->results;
		}
	}
	
