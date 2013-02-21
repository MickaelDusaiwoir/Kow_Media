<?php
	
	require_once('GenericImagesDownloader.class.php');

	class RgbStockImageDownloader extends GenericImagesDownloader
	{
		public function __construct ($keywords = null) 
		{ 
			parent::__construct('www.rgbstock.com','/images/');
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
								else
								{
									trigger_error("La fin du block n'a pas étais trouvée");
								}
							}
							else
							{
								trigger_error("Le début du block n'a pas étais trouvé");
							}
						}
						else
						{
							trigger_error("L'url ne retourne aucune donnnée");
						}				
						
						if ( $results )
						{
							$totalCount += count($results);
							$this->results = array_merge($this->results, $results);
						}
						else
						{
							trigger_error("La regex ne retourne aucune donnnée");
							break;
						}
					}

					return $totalCount;
				}
				else
				{
					trigger_error("Donnez un nombre de page maximum ( setPagination() )");
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

			$startTitle = explode('<p class="ti">', $input);
			$tmp = explode('</p>', $startTitle[1] );
			$data['title'] = strip_tags($tmp[0] );

			$result = array();

			if ( preg_match('#<IMG src="([^"]+)" width="([^"]+)" height="([^"]+)" alt="([^"]+)"#', $input, $result) ) 
			{
				if( strpos($result[1], 'http://') === false )
					$data['image']['thumb_url'] = 'http://a.rgbimg.com'.$result[1];
				else
					$data['image']['thumb_url'] = $result[1];

				$data['image']['width'] = $result[2];
				$data['image']['height'] = $result[3];
				$data['image']['alt'] = $result[4];
			}

			$url = str_replace('100', '300', $data['image']['thumb_url'] );
			$data['image']['url'] = $url;

			return $data;
		}
	}