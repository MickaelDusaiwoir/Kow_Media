<?php
	
	require_once('GenericImagesDownloader.class.php');

	class OfficeImageDownloader extends GenericImagesDownloader
	{
		function __construct ($keywords = null) 
		{ 
			parent::__construct('www.office.microsoft.com','/images/results.aspx?qu=');
			if ( $keywords )
				$this->setKeywords($keywords);
		}

		public function search () 
		{
			if ( $this->keywords ) 
			{	
				if ( $this->numPage )
				{
					$totalCount = 0;

					for ( $j = 1; $j <= $this->numPage ; $j++ ) 
					{ 
						$results = array();

						$url = $this->buildURL();
						$url .= '#pg:'.$j.'|mt:2|is:0|';

						if ( $html = $this->getContent($url) )
						{ 
							if( $start = strpos($html, 'var jsonSearchResults = ') )
							{ 
								if ( $end = strpos($html, ';', $start) ) 
								{ 
									if ( $block = substr($html, $start, $end - $start) ) 
									{	
										$block = str_replace('var jsonSearchResults =','', $block);						
										$block = json_decode($block);	
										print_r($block);
									}
								}
							}
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
					trigger_error("Entrez un nombre de page maximum via la fonction setPagination()");
				}
			}

			return 0;
		}

		protected function buildURL ()
		{
			return $this->domain.'/'.$this->lang.$this->path.$this->keywords.'&ex=2';
		}

	}