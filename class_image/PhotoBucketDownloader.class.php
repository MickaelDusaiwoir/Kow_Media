<?php 

	require_once('GenericImagesDownloader.class.php');

	/**
	* @file PhotoBucketDownloader.class.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief PhotoBucketDownloader class file.  */

	/**
	* @class PhotoBucketDownloader
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief Cette classe permet de télécharger les images des résultats de "photobucket.com". 
	*/	
	class PhotoBucketDownloader extends GenericImagesDownloader
	{
		/** 
		* @brief Le constructeur.
		* @param $keywords Chaine de caractères comportant les mots clés.
		* @details Appel de la fonction setkeywords s'il y a des mots clés. 
		*/
		public function __construct ($keywords = null) 
		{ 
			parent::__construct('http://ww6.photobucket.com','/images/');
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
				$results = array();
				// Création de l'URL et ajout du numéro de page à analyser 
				$url = $this->buildURL().'/';

				// récupération du contenu de la page avant d'extraits le bloc ciblé
				// Parcour de chaque item afin de reupéré les images
				if ( $html = $this->getContent($url) )
				{
					if( ( $start = strpos($html, '<div class="tresults"') ) !== false )
					{ 
						if ( ( $end = strpos($html, '<div class="clearBoth"', $start) ) !== false ) 
						{ 
							if ( $block = substr($html, $start, $end - $start) ) 
							{	
								$items = explode('</a>', $block);
								$count = count($items) -1 ;

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
					$this->results = $results;
					return count($results);
			}
			else
			{
				$errors[] = array($this->keywords, self::NO_KEYWORDS);
			}

			return 0;
		}

		private function getDataItem ($input) 
		{	
			$data = array(
				'title' => null, 
				'image' => array(
					'thumb_url' => null,
					'url' 		=> null, // Grande image
					'alt' 		=> null,
					'width'		=> 0,
					'height' 	=> 0
				)
			);

			$result = array();

			// Recherche des chaines de caractère correspondantes aux expressions régulières.
			// Retourne un tableau soit vide soit rempli celons le résultat.

			if ( preg_match('#<img src="([^"]+)" class="([^"]+)" title="([^"]*)" alt="([^"]+)"#', $input, $result) ) 
			{	
				
				$url = str_replace('/th_', '/', $result[1]);
				$headers = get_headers($url, 1);

				if ( strpos($headers[0], '200') !== false )
				{
					$tmp = str_replace(' ', '%20', $result[1]);
					$headers = get_headers($tmp, 1);

					$data['image']['thumb_url'] = $result[1];
					$data['image']['url'] 		= $url;
					$data['image']['alt'] 		= $result[4];
					$data['title'] 				= $result[3];

					if ( strpos($headers[0], '200') !== false )
					{
						$tmp = getimagesize(str_replace(' ', '%20', $result[1]));
						$data['image']['width'] 	= $tmp[0];
						$data['image']['height'] 	= $tmp[1];
					}

					return $data;
				}			
			}

			return $data = array();
		}

	}