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
					for ( $j = 1; $j <= $this->numPage ; $j++ ) 
					{ 
						$results = array();
						$totalCount = 0;

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
									if ( $j < 2 )
										$errors[] = array($url, self::END_BLOCK_NOT_FOUND);
									else
										return $totalCount;
								}
							}
							else
							{
								if ( $j < 2 )
									$errors[] = array($url, self::START_BLOCK_NOT_FOUND);
								else
										return $totalCount;
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
					$errors[] = self::NO_PAGE_NUMBER;
				}
			}
			else
			{
				$errors[] = self::NO_KEYWORDS;
			}

			return 0;
		}

		private function getDataItem ($input) 
		{	
			$data = array(
				'title' => null, 
				'image' => array(
					'thumb_url' => null,
					'url' => null, // Grande image
					'alt' => null,
					'width' => 0,
					'height' => 0
				)
			);

			$startTitle = explode('<p>', $input);
			$tmp = explode('</p>', $startTitle[1] );
			$data['title'] = strip_tags($tmp[0] );
			$result = array();

			if ( preg_match('#<img class="([^"]+)" src="([^"]+)" alt="([^"]+)" width="([^"]+)" height="([^"]+)"#', $input, $result) ) 
			{	
				$urlImage = explode('var/thumbs/', $result[2]);

				$data['image']['thumb_url'] = 'http://'.$this->domain.$result[2];
				$data['image']['url'] = 'http://'.$this->domain.'/var/resizes/'.$urlImage[1];
				$data['image']['alt'] = $result[3];
				$data['image']['width'] = $result[4];
				$data['image']['height'] = $result[5];
			}

			return $data;
		}
	}
