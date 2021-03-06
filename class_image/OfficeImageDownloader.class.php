<?php
	
	require_once('GenericImagesDownloader.class.php');

	/**
	* @file OfficeImageDownloader.class.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief OfficeImageDownloader class file.  */

	/**
	* @class OfficeImageDownloader
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief Cette classe permet de télécharger les images des résultats de "Office.microsoft.com". 
	*/	
	class OfficeImageDownloader extends GenericImagesDownloader
	{
		/** 
		* @brief Le constructeur.
		* @param $keywords Chaine de caractères comportant les mots clés.
		* @details Appel de la fonction setkeywords s'il y a des mots clés. 
		*/
		public function __construct ($keywords = null) 
		{ 
			parent::__construct('www.office.microsoft.com','/images/results.aspx?qu=');
			if ( $keywords )
				$this->setKeywords($keywords);
		}

		/** 
		* @brief Débute une recherche sur Microsoft office.
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
					$languages = array('French' => 'fr-be', 'English' => 'en-us');

					// Fait un de tour de boucle pour chaque langue dans le tableau
					foreach ($languages as $lang) 
					{						
						$results = array();

						// Création de l'URL et ajout du numéro de page à analyser 
						$this->lang = $lang;
						$url = $this->buildURL();

						// récupération du contenu de la page avant d'extraits le bloc ciblé
						// Parcour de chaque item afin de reupéré les images
						if ( $html = $this->getContent($url) )
						{ 
							if( ( $start = strpos($html, '<div id="dvResults"') ) !== false )
							{ 
								if ( ( $end = strpos($html, '<span class="cdSearchBottomPaging"', $start) ) !== false ) 
								{ 
									if ( $block = substr($html, $start, $end - $start) ) 
									{	
										$items = explode('</a>', $block);
										$count = count($items) -1 ;

										for ( $i = 0; $i < $count; $i++ ) 
										{
											$results[] = $this->getDataItem($items[$i]);
										}
									}
								}
								else
								{
									$errors[] = array($url, self::END_BLOCK_NOT_FOUND);
								}
							}
							else
							{
								$errors[] = array($url,self::START_BLOCK_NOT_FOUND);
							}
						}	
						else
						{
							$errors[] = array($url,self::NO_CONTENT);
						}	

						if ( $results )
						{
							$totalCount += count($results);
							$this->results = array_merge($this->results, $results);
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

		/** 
		* @brief Fonction permettant la création de l'URL.
		*/
		protected function buildURL ()
		{
			return $this->domain.'/'.$this->lang.$this->path.$this->keywords.'&ex=2';
		}

		private function getDataItem ($input) 
		{	
			$result = array();

			// Recherche des chaines de caractère correspondantes aux expressions régulières.
			// Retourne un tableau soit vide soit rempli celons le résultat. 
			if ( preg_match('#<a class="([^"]+)" id="([^"]+)" href="([^"]+)" name="([^"]+)"#', $input, $result) )
			{		
				if ( strpos($result[4], 'MP')  !== false ) // type image 
				{
					// ex: /en-us/images/bull-with-mountains-and-sun-MP900446569.aspx
					// http://officeimg.vo.msecnd.net/en-us/images/bull-with-mountains-and-sun-MP900446569.jpg
					$url = 'http://officeimg.vo.msecnd.net'.str_replace('.aspx', '.jpg', $result[3]);

					$headers = get_headers($url, 1);

					if ( strpos($headers[0], '200') !== false )
					{
						$result = array();

						if ( preg_match('#<img class="([^"]+)" title="" alt="([^"]+)" src="([^"]+)"#', $input, $result) ) 
						{ 
							$tmp = getimagesize('http:'.$result[3]);

							return array(
								'title' => $result[2], 
								'image' => array(
									'thumb_url' => 'http:'.$result[3],
									'url' => $url, // Grande image
									'alt' => $result[2],
									'width' => $tmp[0],
									'height' => $tmp[1]
								)
							);
						}						
					}
				}
			}			
			
			return array();
		}

	}