<?php
	
	require_once('GenericImagesDownloader.class.php');
	
	/**
	* @file GoogleImageDownloader.class.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief GoogleImageDownloader class file.  */

	/**
	* @class GoogleImageDownloader
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief Cette classe permet de télécharger les images des résultats de "Google Image". 
	*/	
	class GoogleImageDownloader extends GenericImagesDownloader
	{
		// Search parameters
		/** @brief $numPage Numéro de la page a recherché */
		protected $numPage = 0;
		/** @brief $imgPerPage Nombre d'image par page de recherche sur le site imagebase.net, sert a la création de la pagination */
		protected $imgPerPage = 20;

		/** 
		* @brief Le constructeur.
		* @param $keywords Chaine de caractères comportant les mots clés.
		* @details Appel de la fonction setkeywords s'il y a des mots clés. 
		*/
		function __construct ( $keywords = null ) 
		{ 
			parent::__construct('www.google.com','/search?tbm=isch&q=');
			if ( $keywords )
				$this->setKeywords( $keywords );
		}


		/** 
		* @brief Débute une recherche sur google image.
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
					for ( $j = 1; $j <= $this->numPage ; $j++ ) 
					{ 
						$results = array();						
						
						// Création de l'URL et ajout du numéro de page à analyser 
						$url = $this->buildURL();
						$url .= '&start='.($j * 20);

						// récupération du contenu de la page avant d'extraits le bloc ciblé
						// Parcour de chaque item afin de reupéré les images
						if( $html = $this->getContent($url) )
						{
							if( ( $start = strpos($html, '<div id="ires"') ) !== false )
							{
								if ( ( $end = strpos($html, '</div>', $start) ) !== false ) 
								{
									if ( $block = substr($html, $start, $end - $start) ) 
									{
										$items = explode('</td>', $block);
										$count = count($items) - 1;

										for ( $i = 0; $i < $count; $i++ ) 
										{ 
											if ( $tmp = $this->getDataItem($items[$i]) )
												$results[] = $tmp;
											else
												$errors[] = array($url, self::INVALID_REGEX);
										}
									}
								}
								else
								{
									$errors = array($url, self::END_BLOCK_NOT_FOUND);
								}
							}
							else
							{
								$errors = array($url, self::START_BLOCK_NOT_FOUND);
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
			
			$result = array();

			if ( preg_match('#/imgres\?imgurl=([^&]+)&amp;#', $input, $result) )	
			{			
				$headers = get_headers($result[1], 1);

				if ( strpos($headers[0], '200') !== false  )
				{
					$results = array();

					if ( preg_match('#<img height="([^"]+)" width="([^"]+)" src="([^"]+)"#', $input, $results) ) 
					{
						return $data = array(
							'title' => null, 
							'image' => array(
								'thumb_url' => $results[3],
								'url' 		=> $result[1], // Grande image
								'alt' 		=> null,
								'width' 	=> $results[2],
								'height' 	=> $results[1]
							)
						);
					}
				}
			}

			return $data = array();
		}
	}
