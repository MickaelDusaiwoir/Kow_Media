<?php

	/**
	* @file GenericImagesDownloader.class.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief GenericImagesDownloader class file. 
	* @brief Classe générique à toute les autres classes
	*/

	/**
	* @class GenericImagesDownloader
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief Cette classe est la classe générique à toutes les autres.
	*/	
	abstract class GenericImagesDownloader 
	{
		/** @brief Erreur aucun mot clé. */
		const NO_KEYWORDS 			= 1;
		/** @brief Erreur aucun nombre de résultat maximum introduit. */
		const NO_RESULTAT_NUMBER 	= 2;
		/** @brief Erreur aucun contenu trouver. */
		const NO_CONTENT 			= 3;
		/** @brief Erreur début du bloc non trouver. */
		const START_BLOCK_NOT_FOUND = 4;
		/** @brief Erreur fin du bloc non trouver. */
		const END_BLOCK_NOT_FOUND   = 5;
		/** @brief Erreur pas de donnée retournée par la regex. */
		const INVALID_REGEX			= 6;

		// Search parameters
		/** @brief $results Tableau contenant les résultats obtenus */
		protected $results 	= array();
		/** @brief $domain Contient l'url du site */
		protected $domain 	= null;
		/** @brief $path Chemin relatif permettant la recherche */
		protected $path 	= null;
		/** @brief $keywords Chaine de caractères comportant les mots clés */
		protected $keywords = null;
		/** @brief $numpage Contient le nombre de pages à parcourir */
		protected $numPage 	= 1;
		/** @brief $curl Contiennent toutes les informations utiles à la fonction curl */
		protected $curl 	= null;
		/** @brief $nbResult Nombre de resultat demander */
		protected $nbResult = 20;

		/** 
		* @brief Le constructeur.
		* @param $domain URL du site 
		* @param $path Chemin relatif permettant la recherche
		* @details Initialisation de curl 
		*/
		public function __construct ($domain, $path) 
		{
			// Initialisation du domaine et du path s'ils sont définis
			if ( $domain )
				$this->domain = $domain;
			else
				trigger_error("Entrez un nom de domaine (Ex : google.be)");

			if ( $path )
				$this->path = $path;
			else
				trigger_error("Entrez un path (Ex : /search?q= )");

			// Initialisation de curl 
			$header[0] 	= "Accept: text/xml,application/xml,application/xhtml+xml,";
			$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
			$header[] 	= "Cache-Control: max-age=0";
			$header[] 	= "Connection: keep-alive";
			$header[] 	= "Keep-Alive: 300";
			$header[] 	= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
			$header[] 	= "Accept-Language: en-us,en;q=0.5";
			$header[] 	= "Pragma: ";

			$this->curl = curl_init();

			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
				
		}

		/** 
		* @brief Déclaration de la fonction recherche.
		* @param $errors Un tableau servant a affiché les possibles erreurs.
		*/
		abstract public function search (array & $errors = array()) ;

		/** 
		* @brief Retourne le tableau d'images.
		* @return Un tableau associatif structuré de cette manière : array("title", "image" => array("thumb_url", "url", "alt", "width", "height"))
		*/
		public function getResults () 
		{
			return $this->results;
		}

		/** 
		* @brief Fonction traitant les mots cles afin de les prépare pour la recherche.
		* @param $keywords Chaine de caractères comportant les mots clés.
		*/
		protected function setKeywords ($keywords) 
		{
			if ( $keywords ) 
			{
				$this->keywords = $keywords;
				$this->keywords = preg_replace('#([ ]{1,})#', '+', $this->keywords);
			}
			else
			{
				trigger_error("Entrez au moins un mot clé");
			}
		}

		/** 
		* @brief Fonction permettant la création de l'URL.
		*/
		protected function buildURL ()
		{
			return $this->domain.$this->path.$this->keywords;
		}

		/** 
		* @brief Fonction exécutant la recherche du contenu depuis l'URL donnée.
		* @param $url URL complète du site sur laquelle la recherche doit s'effectuer.
		*/
		protected function getContent ($url)
		{
			if ( $this->curl )
			{
				curl_setopt($this->curl, CURLOPT_URL, $url);				
				return curl_exec( $this->curl );
			}
			return null;
		}

		/** 
		* @brief Prépare la pagination.
		* @param $nbResult Nombre de résultat a afficher.
		*/
		public function setPagination ( $nbResult )
		{
			if ( $nbResult > 0)
				$this->nbResult = $nbResult;
		}

		/** 
		* @brief Détruit la classe.
		*/
		public function __destruct()
		{
			curl_close($this->curl);
		}
	}
	