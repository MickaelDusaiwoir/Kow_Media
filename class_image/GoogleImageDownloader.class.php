<?php
	
	require_once('GenericImagesDownloader.class.php');
	
	class GoogleImageDownloader extends GenericImagesDownloader
	{
		protected $numPage = 0;

		function __construct ( $keywords = null ) 
		{ 
			parent::__construct('www.google.com','/search?tbm=isch&q=');
			if ( $keywords )
				$this->setKeywords( $keywords );
		}

		public function search (array & $errors = array())
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
						$url .= '&start='.($j * 20);

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
					$errors[] = array($this->numPage, self::NO_PAGE_NUMBER);
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
