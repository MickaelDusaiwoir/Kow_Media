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
					if( $start = strpos($html, '<div class="tresults"') )
					{ 
						if ( $end = strpos($html, '<div class="clearBoth"', $start) ) 
						{ 
							if ( $block = substr($html, $start, $end - $start) ) 
							{	
								$items = explode('</a>', $block);
								$count = count($items) -1 ;

								for ( $i = 0; $i < $count; $i++ ) 
								{
									$results[] = $this->getDataItem($items[$i]);
									$this->results = $results;
								}	

								return count($results);
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

			 '#<div id="([^"]+)" 
					        class="thumbnail" 
					        style="([^"]+)"
					        pbpending="0" 
					        pbthumburl="([^"]+)" 
					        pbthumbtype="search" 
					        pbshowflyout="0" 
					        pbinfo="([^"]+)"
					  #';

			if ( preg_match('#<img src="([^"]+)" class="([^"]+)" title="([^"]+)" alt="([^"]+)"#', $input, $result) ) 
			{				
				$tmp = getimagesize($result[1]);
				$url = str_replace('/th_', '/', $result[1]);

				$data['image']['thumb_url'] = $result[1];
				$data['image']['url'] = $url;
				$data['image']['width'] = $tmp[0];
				$data['image']['height'] = $tmp[1];
				$data['image']['alt'] = $result[4];
				$data['title'] = $result[4];
			}
			else
			{
				trigger_error("La regex ne retourne aucune donnnée");
			}

			return $data;
		}

	}