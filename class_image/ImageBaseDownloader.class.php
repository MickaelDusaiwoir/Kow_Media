<?php
	
	require_once('GenericImagesDownloader.class.php');

	/**
	* @file ImagebaseDownloader.class.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief ImagebaseDownloader class file.  */

	/**
	* @class ImagebaseDownloader
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief Cette classe permet de télécharger les images des résultats de "imagebase.net". 
	*/
	class ImagebaseDownloader extends GenericImagesDownloader
	{
		protected $imgPerPage = 18;

		public function __construct ($keywords = null) 
		{ 
			parent::__construct('www.imagebase.net','/search?q=');
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
					// Calcule le nombre de pages dont on aura besoin afin d'obtenir le nombre de résultat.
					$this->numPage = ceil($this->nbResult / $this->imgPerPage);

					$totalCount = 0;

					// Fait autant de tour de boucle qu'il y a de nombre de pages
					for ( $j = 1; $j <= $this->numPage; $j++ ) 
					{ 
						$results = array();						

						// Création de l'URL et ajout du numéro de page à analyser 
						$url = $this->buildURL();
						$url .= '&page='.$j;

						// récupération du contenu de la page avant d'extraits le bloc ciblé
						// Parcour de chaque item afin de reupéré les images
						if ( $html = $this->getContent($url) )
						{
							if( ( $start = strpos($html, '<ul id="g-album-grid"') ) !== false )
							{
								if ( ( $end = strpos($html, '</ul>', $start) ) !== false ) 
								{
									if ( $block = substr($html, $start, $end - $start) ) 
									{
										$items = explode('</li>', $block);
										$count = count($items) -1;

										for ( $i = 0; $i < $count; $i++ ) 
										{ 
											if ( $tmp =  $this->getDataItem($items[$i]) )
												$results[] = $tmp;
											else
												$errors[] = array($url, self::INVALID_REGEX);
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
								$errors[] = array($url, self::START_BLOCK_NOT_FOUND);
							}
						}
						else
						{
							$errors[] = array($url, self::NO_CONTENT);
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
			
			$startTitle = explode('<p>', $input);
			$tmp = explode('</p>', $startTitle[1] );
			$result = array();

			if ( preg_match('#<img class="([^"]+)" src="([^"]+)" alt="([^"]+)" width="([^"]+)" height="([^"]+)"#', $input, $result) ) 
			{	
				$urlImage = explode('var/thumbs/', $result[2]);
				$headers = get_headers('http://'.$this->domain.'/var/resizes/'.$urlImage[1], 1);

				if ( strpos($headers[0], '200') !== false  ) 
				{
					$data = array(
						'title' => strip_tags($tmp[0]), 
						'image' => array(
							'thumb_url' => 'http://'.$this->domain.$result[2],
							'url' 		=> 'http://'.$this->domain.'/var/resizes/'.$urlImage[1], // Grande image
							'alt' 		=> $result[3],
							'width' 	=> $result[4],
							'height' 	=> $result[5]
						)
					);

					return $data;
				}				
			}

			return $data = array();
		}
	}
