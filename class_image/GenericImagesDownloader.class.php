<?php

	abstract class GenericImagesDownloader 
	{
		const French = 'fr-be';
		const English = 'en-us';

		protected $results = array();
		protected $domain = null;
		protected $path = null;
		protected $page = null;
		protected $keywords = null;
		protected $display = null;
		protected $numPage = 1;
		protected $lang = null;
		protected $curl = null;

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

			$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
			$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
			$header[] = "Cache-Control: max-age=0";
			$header[] = "Connection: keep-alive";
			$header[] = "Keep-Alive: 300";
			$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
			$header[] = "Accept-Language: en-us,en;q=0.5";
			$header[] = "Pragma: ";

			$this->curl = curl_init();

			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
				
		}

		abstract public function search () ;

		public function getResults () 
		{
			return $this->results;
		}

		public function setKeywords ($keywords) 
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

		public function setPagination ( $numPage )
		{
			if ( $numPage > 0)
				$this->numPage = $numPage;
		}

		public function setLanguage ($language)
		{
			switch ($language) 
			{
				case self::French :
				case self::English :
					$this->lang = $language;
					break;
					
				default:
					trigger_error('Entrez une langue (French ou English)');
					break;
			}
		}

		public function __destruct()
		{
				curl_close($this->curl);
		}
	}
	