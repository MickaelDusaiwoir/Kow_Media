<?php
	
	require_once('GenericImagesDownloader.class.php');

	class ImagebaseDownloader extends GenericImagesDownloader
	{
		function __construct ($keywords = null) 
		{ 
			parent::__construct('www.imagebase.net','/search?q=');
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
						$url .= '&page='.$j;

						if ( $html = $this->getContent($url) )
						{
							if( $start = strpos($html, '<ul id="g-album-grid"') )
							{
								if ( $end = strpos($html, '</ul>', $start) ) 
								{
									if ( $block = substr($html, $start, $end - $start) ) 
									{
										$items = explode('</li>', $block);
										$count = count($items) -1;

										for ( $i = 0; $i < $count; $i++ ) 
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
