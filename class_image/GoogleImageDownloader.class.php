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

		public function search ()
		{	
			if ( $this->keywords ) 
			{
				if ( $this->numPage ) 
				{
					$totalCount = 0;		
					$numPage = $this->numPage - 1;

					for ( $j = 0;  $j <= $numPage;  $j++ ) 
					{
						$results = array();
						$url = $this->buildURL();
						$url .= '&start='.($j * 20);
						
						if( $html = $this->getContent($url) )
						{
							if( $start = strpos($html, '<div id="ires"') )
							{
								if ( $end = strpos($html, '</div>', $start) ) 
								{
									if ( $block = substr($html, $start, $end - $start) ) 
									{
										$items = explode('</td>', $block);
										$count = count($items) - 1;

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
			$result = array();

			if ( preg_match('#<img height="([^"]+)" width="([^"]+)" src="([^"]+)"#', $input, $result) ) 
			{
				$data['image']['thumb_url'] = $result[3];
				$data['image']['width'] = $result[2];
				$data['image']['height'] = $result[1];
			}

			$result = array();

			if ( preg_match('#/imgres\?imgurl=([^&]+)&amp;#', $input, $result) )				
				$data['image']['url'] = $result[1];

			return $data;
		}
	}
