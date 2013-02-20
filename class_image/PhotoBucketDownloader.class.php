<?php 

	require_once('GenericImagesDownloader.class.php');

	class PhotoBucketDownloader extends GenericImagesDownloader
	{

		public function __construct ($keywords = null) 
		{ 
			parent::__construct('http://ww6.photobucket.com','/images/');
			if ( $keywords )
				$this->setKeywords($keywords);
		}

		public function search () 
		{
			if ( $this->keywords ) 
			{	 
				$results = array();

				$url = $this->buildURL().'/';

				if ( $html = $this->getContent($url) )
				{ echo $html;
					/*if( $start = strpos($html, '<div id="dvResults"') )
					{ 
						if ( $end = strpos($html, '<span class="cdSearchBottomPaging"', $start) ) 
						{ 
							if ( $block = substr($html, $start, $end - $start) ) 
							{	
								$items = explode('</a>', $block);
								$count = count($items) -1 ;

								for ( $i = 0; $i < $count; $i++ ) 
								{
										$results[] = $this->getDataItem($items[$i]);
								}	

								return count($results);
							}
						}
					}*/
				}
			}

			return 0;

			if ( $this->keywords ) 
			{	
				if ( $this->numPage )
				{
					$totalCount = 0;

					for ( $j = 1; $j <= $this->numPage ; $j++ ) 
					{ 
						$results = array();
						$url = $this->buildURL();
						$url .= '/'.$j;

						if ( $html = $this->getContent($url) )
						{ 
							if( $start = strpos($html, '<DIV class="th"') )
							{
								if ( $end = strpos($html, '<div id="stockfreshresults">', $start) ) 
								{
									if ( $block = substr($html, $start, $end - $start) ) 
									{
										$items = explode('<DIV class="ph"', $block);
										$count = count($items) ;

										for ( $i = 1; $i < $count; $i++ ) 
										{ 
											$results[] = $this->getDataItem($items[$i]);
										}				
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



	}