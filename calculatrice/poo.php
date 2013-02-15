<?php

	abstract class Player
	{
		private $filepath = null;
		
		public function __construct ($filepath = null)
		{
			if ( $filepath != null )
				$this->setFilepath($filepath);
		}
		
		public function setFilepath ($filepath)
		{
			if ( file_exists($filepath) )
				$this->filepath = $filepath;
			else
				trigger_error('Fuck le fichier n\'existe pas !!!');
		}
		
		public function getFilepath ()
		{
			return $this->filepath;
		}
		
		abstract public function play ();
		abstract public function stop ();
		abstract public function pause ();
		
		static public function isSpeakerAvailable ()
		{
			if ( true )
				echo 'Il y a des enceintes !'."\n";
			else
				echo 'Va t\'acheter des enceintes !'."\n";
		}
	}
	
	class VideoPlayer extends Player
	{
		public function __construct ($filepath = null)
		{
			parent::__construct($filepath);
		}
		
		public function play ()
		{
			echo 'play !'."\n";
		}
		
		public function stop ()
		{
			echo 'stop !'."\n";
		}
		
		public function pause ()
		{
			echo 'pause !'."\n";
		}
	}
	
	class AudioPlayer extends Player
	{
		public function __construct ($filepath = null)
		{
			parent::__construct($filepath);
		}
		
		public function play ()
		{
			echo 'play !'."\n";
		}
		
		public function stop ()
		{
			echo 'stop !'."\n";
		}
		
		public function pause ()
		{
			echo 'pause !'."\n";
		}
	}
	
	$a = new VideoPlayer();
	$a->setFilepath("circus.jpg");
	echo $a->getFilepath()."\n";
	$a->play();
	
	$b = new VideoPlayer();
	$b->setFilepath("header_mail2.xcf");
	echo $b->getFilepath()."\n";
	$b->play();
	
	echo $a->getFilepath()."\n";
	
	VideoPlayer::getFilepath();
	
	$a->isSpeakerAvailable();
	AudioPlayer::isSpeakerAvailable();
	VideoPlayer::isSpeakerAvailable();
	
