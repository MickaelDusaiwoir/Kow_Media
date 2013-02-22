<?php
	
	require_once('GenericImagesDownloader.class.php');

	class OfficeImageDownloader extends GenericImagesDownloader
	{
		public function __construct ($keywords = null) 
		{ 
			parent::__construct('www.office.microsoft.com','/images/results.aspx?qu=');
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
					$languages = array('French' => 'fr-be', 'English' => 'en-us');

					foreach ($languages as $lang) 
					{						
						for ( $j = 1; $j <= $this->numPage ; $j++ ) 
						{ 
							$results = array();

							$this->lang = $lang;
							$url = $this->buildURL();
							$url .= '#pg:'.$j;

							if ( $html = $this->getContent($url) )
							{ 
								if( ( $start = strpos($html, '<div id="dvResults"') ) !== false )
								{ 
									if ( ( $end = strpos($html, '<span class="cdSearchBottomPaging"', $start) ) !== false ) 
									{ 
										if ( $block = substr($html, $start, $end - $start) ) 
										{	
											$items = explode('</a>', $block);
											$count = count($items) -1 ;

											for ( $i = 0; $i < $count; $i++ ) 
											{
												$results[] = $this->getDataItem($items[$i]);
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
									$errors[] = array($url,self::START_BLOCK_NOT_FOUND);
								}
							}	
							else
							{
								$errors[] = array($url,self::NO_CONTENT);
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

		protected function buildURL ()
		{
			return $this->domain.'/'.$this->lang.$this->path.$this->keywords.'&ex=2';
		}

		private function getDataItem ($input) 
		{	
			$result = array();

			if ( preg_match('#<a class="([^"]+)" id="([^"]+)" href="([^"]+)" name="([^"]+)"#', $input, $result) )
			{		
				if ( strpos($result[4], 'MP')  !== false ) // type image 
				{
					// ex: /en-us/images/bull-with-mountains-and-sun-MP900446569.aspx
					// http://officeimg.vo.msecnd.net/en-us/images/bull-with-mountains-and-sun-MP900446569.jpg
					$url = 'http://officeimg.vo.msecnd.net'.str_replace('.aspx', '.jpg', $result[3]);
					$result = array();

					if ( preg_match('#<img class="([^"]+)" title="" alt="([^"]+)" src="([^"]+)"#', $input, $result) ) 
					{ 
						$tmp = getimagesize('http:'.$result[3]);

						return array(
							'title' => $result[2], 
							'image' => array(
								'thumb_url' => 'http:'.$result[3],
								'url' => $url, // Grande image
								'alt' => $result[2],
								'width' => $tmp[0],
								'height' => $tmp[1]
							)
						);
					}
				}
			}			
			
			return array();
		}

	}