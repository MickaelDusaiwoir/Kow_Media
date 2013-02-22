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

		public function search (array & $errors = array()) 
		{
			if ( $this->keywords ) 
			{	 
				$results = array();
				$url = $this->buildURL().'/';

				if ( $html = $this->getContent($url) )
				{
					if( ( $start = strpos($html, '<div class="tresults"') ) !== false )
					{ 
						if ( ( $end = strpos($html, '<div class="clearBoth"', $start) ) !== false ) 
						{ 
							if ( $block = substr($html, $start, $end - $start) ) 
							{	
								$items = explode('</a>', $block);
								$count = count($items) -1 ;

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
					$this->results = $results;
					return count($results);
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
					'url' 		=> null, // Grande image
					'alt' 		=> null,
					'width'		=> 0,
					'height' 	=> 0
				)
			);

			$result = array();

			if ( preg_match('#<img src="([^"]+)" class="([^"]+)" title="([^"]*)" alt="([^"]+)"#', $input, $result) ) 
			{	
				$tmp = getimagesize(str_replace(' ', '%20', $result[1]));
				$url = str_replace('/th_', '/', $result[1]);

				$data['image']['thumb_url'] = $result[1];
				$data['image']['url'] 		= $url;
				$data['image']['width'] 	= $tmp[0];
				$data['image']['height'] 	= $tmp[1];
				$data['image']['alt'] 		= $result[4];
				$data['title'] 				= $result[3];
			}

			return $data;
		}

	}