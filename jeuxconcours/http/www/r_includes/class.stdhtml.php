<?php
	
	/*
		CLASS StdHTML - v1.1 (11/05/2010)
		Author : LondNoir (londnoir@sdmedia.be)
		
		Standard HTML page class.

		=============================================
		|| DEFINES required by the class,          ||
		|| these must be defined in a config file. ||
		=============================================

		define('STDHTML_MODULES_PATH', 'r_modules/');
	*/
	
	class stdhtml
	{
		private $style = 'default';
		private $layout = 'default';
		private $htmlPath = NULL;
		
		/* META Informations */
		private $metaTitle = NULL;
		private $metaDescription = NULL;
		
		private $cssFiles = array();
		private $cssEmbed = array();
		
		private $jsFiles = array();
		private $jsEmbed = array();
		
		private $storage = array();
		
		private $mBus;
		
		function __construct (mbus $mBus, $title = 'Untitled document', $layout = NULL, $style = NULL)
		{
			$this->mBus = $mBus;
			$this->metaTitle = $title;
			
			$path = 'r_html/';
			
			if ( $style && is_dir(_SITE_PATH.$path.$style.'/') )
				$this->style = $style;
				
			$path .= $this->style.'/';
			$this->add_css_file(_SITE_URL.$path.'css/common.css', 'screen');
			
			if ( $layout && is_dir(_SITE_PATH.$path.$layout.'/') )
				$this->layout = $layout;
			
			$path .= $this->layout.'/';
			
			$this->htmlPath = _SITE_PATH.$path.'index.html';
			$this->add_css_file(_SITE_URL.$path.'layout.css', 'screen');
		}
		
		private function build_headers ()
		{
			$HTMLHeaders = NULL;
			
			$HTMLHeaders .= "\t\t".'<title>'.$this->metaTitle.'</title>'."\n";
			$HTMLHeaders .= "\t\t".'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
			if ( $this->cssFiles || $this->cssEmbed )
				$HTMLHeaders .= "\t\t".'<meta http-equiv="Content-Style-Type" content="text/css" />'."\n";
			if ( $this->jsFiles || $this->jsEmbed )
				$HTMLHeaders .= "\t\t".'<meta http-equiv="Content-Script-Type" content="text/javascript" />'."\n";
			$HTMLHeaders .= "\t\t".'<meta name="language" content="fr" />'."\n";
			if ( $this->metaDescription )
			{
				$HTMLHeaders .= "\t\t".'<meta name="Description" content="'.$this->metaDescription.'" />'."\n";
			}
			/* Icon */
			$HTMLHeaders .= "\t\t".'<!-- URL ICON -->'."\n";
			$HTMLHeaders .= "\t\t".'<link rel="icon" type="image/png" href="favicon.png" />'."\n";
			
			//$HTMLHeaders .= "\t\t".'<!-- FLUX RSS -->'."\n";
			
			/* CSS handler */
			if ( $this->cssFiles )
			{
				$HTMLHeaders .= "\t\t".'<!-- Cascade Style Sheets -->'."\n";
				
				foreach ( $this->cssFiles as $css )
				{
					$HTMLHeaders .= "\t\t".'<link type="text/css" rel="stylesheet" href="'.$css['file'].'" media="'.$css['type'].'" />'."\n";
				}
			}
			if ( $this->cssEmbed )
			{
				$HTMLHeaders .= "\t\t".'<!-- Embed Cascade Style Sheets -->'."\n";
				
				foreach ( $this->cssEmbed as $embed )
				{
					$HTMLHeaders .= "\t\t".'<style type="text/css" media="'.$embed['type'].'">'."\n";
					$HTMLHeaders .= "\t\t".'<!--'."\n";
					$HTMLHeaders .= $embed['content'];
					$HTMLHeaders .= "\t\t".'-->'."\n";
					$HTMLHeaders .= "\t\t".'</style>'."\n";
				}
			}
			
			/* Javascript handler */
			if ( $this->jsFiles )
			{
				$HTMLHeaders .= "\t\t".'<!-- Javascript -->'."\n";
				
				foreach ( $this->jsFiles as $file )
				{
					$HTMLHeaders .= "\t\t".'<script type="text/javascript" src="'.$file.'"></script>'."\n";
				}
			}
			if ( $this->jsEmbed )
			{
				$HTMLHeaders .= "\t\t".'<!-- Embed Javascript -->'."\n";
				
				foreach ( $this->jsEmbed as $embed )
				{
					$HTMLHeaders .= "\t\t".'<script type="text/javascript">'."\n";
					$HTMLHeaders .= "\t\t".'//<![CDATA['."\n";
					$HTMLHeaders .= $embed;
					$HTMLHeaders .= "\t\t".'//]]>'."\n";
					$HTMLHeaders .= "\t\t".'</script>'."\n";
				}
			}
			
			return $HTMLHeaders;
		}
		
		public function add_css_file ($filepath, $media = 'screen')
		{
			/* FIXME : verifier l'URL */
			if ( $filepath )
			{
				$this->cssFiles[] = array(
					'file' => $filepath,
					'type' => $media
					);
			}
			else
			{
				$this->mBus->add(_MSG_ERROR, 'CSS file '.$filepath.' don\'t exists !');
			}
		}
		
		public function embed_css_content ($content, $media = 'screen')
		{
			$this->cssEmbed[] = array(
				'content' => $content,
				'type' => $media
				);
		}
		
		public function add_js_file ($filepath)
		{
			if ( file_exists($filepath) )
			{
				$this->jsFiles[] = $filepath;
			}
			else
			{
				$this->mBus->add(_MSG_ERROR, 'JS file '.$filepath.' don\'t exists !');
			}
		}
		
		public function embed_js_content ($content)
		{
			$this->jsEmbed[] = $content;
		}
		
		public function store ($buffer, $area = 'content')
		{
			if ( isset($this->storage[$area]) )
			{
				$this->storage[$area] .= $buffer;
			}
			else
			{
				$this->storage[$area] = $buffer;
			}
		}

		public function store_module ($moduleName, $area = 'content')
		{
			$filepath = STDHTML_MODULES_PATH.$moduleName.'/index.php';
			
			if ( !file_exists($filepath) )
				$this->mBus->add(_MSG_ERROR, 'La classe tente d\'inclure un module qui n\'existe pas.', 'stdhtml::store_module()');
			else
				$this->file_include($filepath, $area);
		}

		public function file_include ($filepath, $area = 'content')
		{
			ob_start();
			include($filepath);
			$buffer = ob_get_contents();
			ob_end_clean();
			
			$this->store($buffer, $area);
		}

		public function display_notifications ($area = 'content')
		{
			$tmp = $this->mBus->get_boxed_notifications();
			
			if ( $tmp )
				$this->store($tmp, $area);
		}
		
		public function get_html ()
		{
			$html = file_get_contents($this->htmlPath);
			
			/* Find {html_meta} and replace
			it with builded meta */
			$html = str_replace('{meta}', $this->build_headers(), $html);
			
			foreach ( $this->storage as $area => $content )
				$html = str_replace('{'.$area.'}', $content, $html);
			
			
			return $html;
		}
		
		function __desctruct ()
		{
			
		}
	}

	class stdbox
	{
		private $title = NULL;
		private $content = NULL;
		private $tools = array();
		private $isRound;
		
		function __construct ($content, $isRound = TRUE)
		{
			$this->content = $content;
			$this->isRound = $isRound ? TRUE : FALSE;
		}

		public function set_title ($title)
		{
			$this->title = $title;
		}

		public function add_link ($url, $link = NULL, $title = NULL, $newWindow = FALSE)
		{
			if ( $link )
			{
				if ( !$title )
				{
					$title = $link;
				}
			}
			else
			{
				$link = $url;
			}
			
			$this->tools[] = "\t\t\t\t\t\t".' <a href="'.$url.'" '.( $title ? 'title="'.$title.'"' : NULL ).( $newWindow ? ' target="_blank"' : NULL ).'>'.$link.'</a> '."\n";
		}

		public function get_html ()
		{
			$buffer = NULL;
			
			$buffer  = "\t\t\t\t".'<div class="box_rounded">'."\n";
			if ( $this->title )
				$buffer .= "\t\t\t\t\t".'<h2 class="box_title">'.$this->title.'</h2>'."\n";
			$buffer .= "\t\t\t\t\t".'<div class="box_content">'."\n";
			$buffer .= $this->content;
			$buffer .= "\t\t\t\t\t".'</div>'."\n";
			if ( $this->tools )
			{
				$buffer .= "\t\t\t\t\t".'<p class="box_cmd">'."\n";
				foreach ( $this->tools as $tool )
					$buffer .= $tool;
				$buffer .= "\t\t\t\t\t".'</p>'."\n";
			}
			$buffer .= "\t\t\t\t".'</div>'."\n";
			
			return $buffer;
		}
	}

?>
