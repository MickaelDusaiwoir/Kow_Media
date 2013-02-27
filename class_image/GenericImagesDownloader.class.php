<?php

	/**
	* @file GenericImagesDownloader.class.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (27/02/2013)
	* @brief GenericImagesDownloader class file. 
	* @brief Classe générique à toute les autres classes
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
		protected $results 	= array();
		protected $domain 	= null;
		protected $path 	= null;
		protected $page 	= null;
		protected $keywords = null;
		protected $display 	= null;
		protected $numPage 	= 1;
		protected $lang 	= null;
		protected $curl 	= null;
		protected $nbResult = 20;

		/** 
		* @brief Le constructeur.
		* @param $domaine URL du site 
		* @param $path Chemin relatif permettant la recherche
		* @details Initialisation de curl 
		*/
		public function __construct ($domain, $path) 
		{
			if ( $domain )
				$this->domain = $domain;
			else
				trigger_error("Entrez un nom de domaine (Ex : google.be)");

			if ( $path )
				$this->path = $path;
			else
				trigger_error("Entrez un path (Ex : /search?q= )");

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

		abstract public function search (array & $errors = array()) ;

		/** 
		* @brief Retourne le tableau d'images.
		* @return Un tableau associatif structuré de cette manière : array("thumb", "width", "height", "url")
		*/
		public function getResults () 
		{
			return $this->results;
		}

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

		protected function buildURL ()
		{
			return $this->domain.$this->path.$this->keywords;
		}

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
	