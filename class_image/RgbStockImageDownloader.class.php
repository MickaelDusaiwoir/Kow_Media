<?php
	
	require_once('GenericImagesDownloader.class.php');

	/**
	* @file RgbStockImageDownloader.class.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief RgbStockImageDownloader class file.  */

	/**
	* @class RgbStockImageDownloader
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief Cette classe permet de télécharger les images des résultats de "rgbstock.com". 
	*/
	class RgbStockImageDownloader extends GenericImagesDownloader
	{
		/** 
		* @brief Le constructeur.
		* @param $keywords Chaine de caractères comportant les mots clés.
		* @details Appel de la fonction setkeywords s'il y a des mots clés. 
		*/
		public function __construct ($keywords = null) 
		{ 
			parent::__construct('www.rgbstock.com','/images/');
			if ( $keywords )
				$this->setKeywords($keywords);
		}

		/** 
		* @brief Débute une recherche sur photoBucket.
		* @param $errors Un tableau servant a affiché les possibles erreurs.
		* @return Un nombre entier pour le nombre d'image trouvée ou false en cas de problème.
		*/
		public function search (array & $errors = array())
		{	
			// Vérifie si on a des mots clés
			if ( $this->keywords ) 
			{	
				// Vérifier si on a un nombre de résultat souhaité
				if ( $this->nbResult )
				{
					$totalCount = 0;

					for ( $j = 1; $j <= $this->numPage ; $j++ ) 
					{ 
						$results = array();
						// Création de l'URL et ajout du numéro de page à analyser 
						$url = $this->buildURL();
						$url .= '/'.$j;

						// récupération du contenu de la page avant d'extraits le bloc ciblé
						// Parcour de chaque item afin de reupéré les images
						if ( $html = $this->getContent($url) )
						{ 
							if( ( $start = strpos($html, '<DIV class="th"') ) !== false )
							{
								if ( ( $end = strpos($html, '<div id="stockfreshresults">', $start) ) !== false ) 
								{
									if ( $block = substr($html, $start, $end - $start) ) 
									{
										$items = explode('<DIV class="ph"', $block);
										$count = count($items) ;

										for ( $i = 1; $i < $count; $i++ ) 
										{ 
											if ( $tmp = $this->getDataItem($items[$i]) )
													$results[] = $tmp;
										}
									}
								}
								else
								{
									$errors = self::END_BLOCK_NOT_FOUND;
								}
							}
							else
							{
								$errors = self::START_BLOCK_NOT_FOUND;
							}
						}	
						else
						{
							$errors[] = self::NO_CONTENT;
						}				
						
						if ( $results )
						{
							$totalCount += count($results);
							$this->results = array_merge($this->results, $results);
						}
						else
						{
							break;
						}
					}

					return $totalCount;
				}
				else
				{
					$errors[] = array($this->numPage, self::NO_RESULTAT_NUMBER);
				}
			}
			else
			{
				$errors[] = array($this->keywords, self::NO_KEYWORDS);
			}
			
			return 0;
		}

		private function getDataItem ($input) 
		{	
			// Recherche des chaines de caractère correspondantes aux expressions régulières.
			// Retourne un tableau soit vide soit rempli celons le résultat.

			$startTitle = explode('<p class="ti">', $input);
			$tmp = explode('</p>', $startTitle[1] );

			$result = array();
		
			if ( preg_match('#<IMG src="([^"]+)" width="([^"]+)" height="([^"]+)" alt="([^"]+)"#', $input, $result) ) 
			{
				if( strpos($result[1], 'http://') === false )
					$thumb_url = 'http://a.rgbimg.com'.$result[1];
				else
					$thumb_url = $result[1];

				$url = str_replace('100', '300', $thumb_url );

				$headers = get_headers($url, 1);

				if ( strpos($headers[0], '200') !== false )
				{
					return 	$data = array(
						'title' => strip_tags($tmp[0] ), 
						'image' => array(
							'thumb_url' => $thumb_url,
							'url' 		=> $url, // Grande image
							'alt' 		=> $result[4],
							'width' 	=> $result[2],
							'height' 	=> $result[3]
						)
					);
				}
			}

			return $data = array();
		}
	}