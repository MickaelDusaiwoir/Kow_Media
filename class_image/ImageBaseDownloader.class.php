<?php
	
	require_once('GenericImagesDownloader.class.php');

	class ImagebaseDownloader extends GenericImagesDownloader
	{
		public function __construct ($keywords = null) 
		{ 
			parent::__construct('www.imagebase.net','/search?q=');
			if ( $keywords )
				$this->setKeywords($keywords);
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
						$url .= '&page='.$j;

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
