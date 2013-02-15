<?php 

abstract class GenericImagesDownloader 
{
	protected $results = array();
	protected $domain = null;
	protected $path = null;
	protected $page = null;
	protected $url = null;

	public function __construct ( $domain, $path ) 
	{
		if ( $domain )
			$this->domain = $domain;
		else
			trigger_error("il faut absolument une url");

		if ( $path )
			$this->path = $path;
		else
			trigger_error("il faut absolument un path (ex : /search?q=)");

		$this->url = $this->domain.$this->path;
		echo $this->url;
	}

	abstract public function search ($keyword) ;

	public function getResults () 
	{
		return $this->results;
	}

	protected function getContent ()
	{
		if ( $curl = curl_init() )
		{
			$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
			$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
			$header[] = "Cache-Control: max-age=0";
			$header[] = "Connection: keep-alive";
			$header[] = "Keep-Alive: 300";
			$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
			$header[] = "Accept-Language: en-us,en;q=0.5";
			$header[] = "Pragma: ";
			
			curl_setopt($curl, CURLOPT_URL, $this->url);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			
			$html = curl_exec($curl);
			curl_close($curl);

			return $html;
		}
		return null;
	}

}

class ImagebaseDownloader extends GenericImagesDownloader
{
	protected $keywords = null;
	protected $page = 'null';

	function __construct () 
	{ 
		parent::__construct('www.imagebase.net','/search?q=');

	}

	public function search ($keyword)
	{	
		if ($keyword) 
		{	
			$this->keyword = $keyword;
			$keyword = preg_replace('#([ ]{1,})#', '+', $keyword);
			$this->url = $this->domain.$this->path.$keyword.$this->page;

			for ($i = 1; $i < 2; $i++) 
			{ 
				echo "hi";
				if($html = $this->getContent() )
				{
					if( $start = strpos($html, '<ul id="g-album-grid"') )
					{
						if ( $end = strpos($html, '</ul>', $start)) 
						{
							if ( $block = substr($html, $start, $end - $start) ) 
							{
								$results = array();
								$items = explode('</li>', $block);
								$count = count($items) -1;

								for ($i=0; $i < $count; $i++) 
								{ 
									$results[] = $this->getDataItem( $items[$i] );
								}

								$this->page = '&page='.$i;
								$this->results = $results;
								$this->search($this->keyword);
								//return count($results);
							}
						}
					}
					elseif ( $this->results ) 
					{
						$this->i = $this->i + 10 ;
						return count($results);
					}
				}
				elseif ( $this->results ) 
				{
					$this->i = $this->i + 10 ;
					return count($results);
				}

			$this->i = $this->i + 10 ;
			return null;
			
			} 
		}

		return null;
	}

	private function getDataItem ($input) 
	{	
		$data = array('title' => null , 'image' => array() );

		$startTitle = explode('<p>', $input);
		$tmp = explode('</p>', $startTitle[1] );
		$data['title'] = strip_tags($tmp[0] );

		$result = array();

		if (preg_match('#<img class="([^"]+)" src="([^"]+)" alt="([^"]+)" width="([^"]+)" height="([^"]+)"#', $input, $result)) 
		{	
			$data['image']['class'] = $result[1];
			$data['image']['src'] = 'http://'.$this->domain.$result[2];
			$data['image']['alt'] = $result[3];
			$data['image']['width'] = $result[4];
			$data['image']['height'] = $result[5];
		}

		return $data;
	}
}

class GoogleImageDownloader extends GenericImagesDownloader
{

	function __construct () 
	{ 
		parent::__construct('www.google.be','/search?tbm=isch&q=');

	}

	public function search ($keyword)
	{	
		if ($keyword) 
		{	
			$keyword = preg_replace('#([ ]{1,})#', '+', $keyword);
			$this->url = $this->domain.$this->path.$keyword;

			if($html = $this->getContent() )
			{
				if( $start = strpos($html, '<div id="ires"') )
					if ( $end = strpos($html, '</div>', $start)) 
						if ( $block = substr($html, $start, $end - $start) ) 
						{
							$results = array();
							$items = explode('</td>', $block);
							$count = count($items) -1;
							for ($i=0; $i < $count; $i++) 
							{ 
								$results[] = $this->getDataItem( $items[$i] );
							}

							$this->results = $results;
							return count($results);
						}
						else 
							return null;
					else
						return null;			
				else
					return null;
			}
			else
				return null; 
		}

		return null;
	}

	private function getDataItem ($input) 
	{	
		$data = array('title' => null, 'image' => array() );
		$result = array();

		if (preg_match('#<img height="([^"]+)" width="([^"]+)" src="([^"]+)"#', $input, $result)) 
		{
			$data['image']['src'] = $result[3];
			$data['image']['width'] = $result[2];
			$data['image']['height'] = $result[1];
		}

		return $data;
	}
}

$results = array();
$display = null;

$keyword = 'winter snow';
$img = new ImagebaseDownloader();
//$img = new GoogleImageDownloader();

if($img->search($keyword) > 0)
	if( $results = $img->getResults() )
	{	
		$display .= '<ul>';
		foreach ($results as $result) {

			$display .= '<li>';

			if ( $result['image'] ) 
				$display .= '<img src="'.$result['image']['src'].'" width="'.$result['image']['width'].'" height="'.$result['image']['height'].'" />';

			if ( $result['title'] )
				$display .= '<span>'.$result['title'].'</span>';

			$display .= '</li>';
		}
		$display .= '</ul>';
	}
else 
	$display = 'Nothing was found with these keywords';

echo $display;

?>