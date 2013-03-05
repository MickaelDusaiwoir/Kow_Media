<?php
	
	/*
		CLASS Logs System - v1.2 (28/04/2010)
		Author : LondNoir (londnoir@sdmedia.be)
		
		High-level log system. These messages are intended to logs
		application errors and not substitute the PHP/Apache errors.
		
		NOTE : The log file are saved at class destruction.
		
		=============================================
		|| DEFINES required by the class,          ||
		|| these must be defined in a config file. ||
		=============================================
		
		define('_SAVE_LOG', 1);
		define('_LOG_FILE', '/home/crazy/sites/konkours/http/testing/log/testing.txt'); 
		define('_WARN_MSG', 'L\'éxécution de la page a rencontré un erreur et ne peut pas continuer, veuillez réessayer plus tard. Si l\'erreur persiste, contactez l\'administrateur du site explicant le problème rencontré, merci de votre compréhension.');
	*/
	
	/* These defines must stay here */
	define('_MSG_SUCCESS', 0);
	define('_MSG_ERROR', 1);
	define('_MSG_INFO', 2);
	
	define('_MSG_TO_SCREEN', 0);
	define('_MSG_TO_FILE', 1);
	define('_MSG_TO_BOTH', 2);
	
	define('_TXT_MODE', 0);
	define('_HTML_MODE', 1);
	
	class mbus
	{
		/* This array holds all messages wich 
		will be saved into log file. */
		private $messages = array();
		
		/* This array holds all notifications which
		will be displayed on screen. */
		private $notifications = array();
		
		/* Unique informations for all current messages */
		private $userIP;
		private $scriptName;
		
		private $logFile;
		
		/* Set this before add a message to log.
		0 = on screen
		1 = to file
		2 = both */
		private $target;
		
		/* Messages display mode.
		0 = TXT
		1 = HTML */
		private $displayMode = 1;
		
		/* HTML enjolivement. Direct after <body> tag */
		private $indent = "\t\t";
		
		/* CONSTRUCTOR */
		function __construct ($logFile = _LOG_FILE)
		{
			/* Check file existence */
			if ( !file_exists($logFile) )
			{
				if ( !touch($logFile) )
				{
					trigger_error($logFile.' log file creation failed.');
					
					if ( $GLOBALS['_debug'] )
						echo 'Log file creation error, using default configuration.'."\n";
				}
			}
			
			$this->logFile = $logFile;
			
			/* Check file write permisssion */
			if ( !is_writable($this->logFile) )
			{
				trigger_error('Writting permission denied in '.$this->logFile);
				
				if ( $GLOBALS['_debug'] )
					echo 'Log file writting permission error in '.$this->logFile.'.'."\n";
			}
			
			$this->userIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknow';
			$this->scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : 'unknow';
			$this->target = $GLOBALS['_debug'] ? 2 : 1;
		}
		
		/* Give correct CSS style for message type */
		private function get_css_class ($type)
		{
			switch ( $type )
			{
				case 0 :
					return 'message_positive';
					break;
			
				case 1 :
					return 'message_negative';
					break;
			
				case 2 :
				default :
					return 'message_note';
					break;
			}
		}
		
		/* Give correct label for message type */
		private function get_label ($type)
		{
			switch ( $type )
			{
				case 0 :
					return 'SUCCESS';
					break;
			
				case 1 :
					return 'ERROR';
					break;
			
				case 2 :
				default :
					return 'INFO';
					break;
			}
		}
		
		/* Array message to text */
		private function message_to_text ($array)
		{
			return $array['time'].'__'.$this->userIP.'__'.$array['type'].'__'.$this->scriptName.( $array['details'] ? '->'.$array['details'] : NULL ).'__'.$array['message']."[END]\n";
		}
		
		/* Array message to HTML format */
		private function message_to_html ($array)
		{
			return $this->indent.'<p class="'.$this->get_css_class($array['type']).'" title="'.$this->userIP.'"><i>'.$this->scriptName.( $array['details'] ? ' =&gt; '.$array['details'] : NULL ).'</i> - '.date('d-m-Y H:i:s', $array['time']).'<br />'.$array['message'].'</p>'."\n";
		}
		
		/* Array notification to text */
		private function notification_to_text ($array)
		{
			return '['.$this->get_label($array['type']).'] '.$array['message']."\n";
		}
		
		/* Array notification to HTML format */
		private function notification_to_html ($array)
		{
			return $this->indent.'<p class="'.$this->get_css_class($array['type']).'">'.$array['message'].'</p>'."\n";
		}
		
		/* Save messages array to file */
		private function write_log_file ()
		{
			if ( !$this->messages )
				return TRUE;
			
			/* Open de file in append mode */
			if ( $handle = fopen($this->logFile, 'a') )
			{
				foreach ( $this->messages as $message )
					fwrite($handle, $this->message_to_text($message));
				
				fclose( $handle );
				
				return TRUE;
			}
			else
			{
				trigger_error('error with '.$this->logFile.'.');
				
				if ( $GLOBALS['_debug'] )
					echo 'mBus file opening error'."\n";
				
				return FALSE;
			}
		}
		
		/* Set where the next messages added will go */
		public function set_target ($target)
		{
			$target = intval($target);
			
			switch ( $target )
			{
				/* On screen */
				case 0 :
				/* To file */
				case 1 :
				/* Both */
				case 2 :
					$this->target = $target;
					return TRUE;
					break;
					
				default :
					/* Target still unchanged */
					return FALSE;
					break;
			}
		}
		
		/* Set how message are displayed */
		public function set_display_mode ($displayMode)
		{
			$displayMode = intval($displayMode);
			
			switch ( $displayMode )
			{
				/* Text */
				case 0 :
				/* HTML */
				case 1 : 
					$this->displayMode = $displayMode;
					return TRUE;
					break;
					
				default :
					/* Target still unchanged */
					return FALSE;
					break;
			}
		}
		
		/* Set indentation for HTML document */
		public function set_indent ($count)
		{
			/* Clean indents */
			$this->indent = NULL;
			
			for ($i = 0; $i < $count; $i++)
				$this->indent .= "\t";
		}
		
		/* Add execution message. */
		public function add ($type, $message, $details = NULL)
		{
			$type = intval($type);
			
			$out = FALSE;
			
			/* Add message for the log file */
			if ( $this->target > 0 )
			{
				$this->messages[] = array(
					'time' => time(),
					'details' => $details,
					'message' => $message,
					'type' => $type
				);
				
				$out = TRUE;
			}
			
			/* Add message for notifications */
			if ( $this->target == 0 || $this->target == 2 )
			{
				$this->notifications[] = array(
					'message' => $message,
					'type' => $type
				);
				
				$out = TRUE;
			}
			
			return $out;
		}
		
		/* Display on screen message. */
		public function notification ($type, $message)
		{
			$type = intval($type);
			
			$this->notifications[] = array(
				'message' => $message,
				'type' => $type
			);
		}
		
		/* Return messages array into string */
		public function get_messages ($type = -1)
		{
			$buffer = NULL;
			
			switch ( $this->displayMode )
			{
				/* Text */
				case 0 :
					if ( $type == -1 )
					{
						foreach ( $this->messages as $message )
							$buffer .= $this->message_to_text($message);
					}
					else
					{
						foreach ( $this->messages as $message )
						{
							if ( $message['type'] == $type )
								$buffer .= $this->message_to_text($message);
						}
					}
					break;
					
				/* HTML */
				case 1 : 
					if ( $type == -1 )
					{
						foreach ( $this->messages as $message )
							$buffer .= $this->message_to_html($message);
					}
					else
					{
						foreach ( $this->messages as $message )
						{
							if ($message['type'] == $type)
								$buffer .= $this->message_to_html($message);
						}
					}
					break;
					
				default :
					/* do nothing */
					break;
			}
			
			return $buffer;
		}
		
		/* Return notifications */
		public function get_notifications ( $type = -1 )
		{
			$buffer = NULL;
			
			switch ( $this->displayMode )
			{
				case 0 : /* Text */
					if ($type == -1)
					{
						foreach ( $this->notifications as $message )
							$buffer .= $this->notification_to_text($message);
					}
					else
					{
						foreach ( $this->notifications as $message )
						{
							if ( $message['type'] == $type )
								$buffer .= $this->notification_to_text($message);
						}
					}
					break;
					
				case 1 : /* HTML */
					if ( $type == -1 )
					{
						foreach ( $this->notifications as $message )
							$buffer .= $this->notification_to_html($message);
					}
					else
					{
						foreach ( $this->notifications as $message )
						{
							if ( $message['type'] == $type )
								$buffer .= $this->notification_to_html($message);
						}
					}
					break;
					
				default :
					/* do nothing */
					break;
			}
			
			return $buffer;
		}
		
		/* Return HTML boxed notifications */
		public function get_boxed_notifications ($type = -1)
		{
			$buffer = NULL;
			
			if ( $this->displayMode != 1 )
				return NULL;
			
			if ( $type == -1 )
			{
				$pos = NULL;
				$neg = NULL;
				$nfo = NULL;
				
				/* Fill in three distincts vars */
				foreach ( $this->notifications as $message )
				{
					switch ( $message['type'] )
					{
						case _MSG_SUCCESS :
							$pos .= "\t".$this->notification_to_html($message);
							break;
							
						case _MSG_ERROR :
							$neg .= "\t".$this->notification_to_html($message);
							break;
							
						case _MSG_INFO :
							$nfo .= "\t".$this->notification_to_html($message);
							break;
							
						default :
							break;
					}
				}
				
				/* Check vars and fill the buffer */
				if ( $pos )
				{
					$buffer .= $this->indent.'<div class="positive_box">'."\n";
					$buffer .= $this->indent."\t".'<br />'."\n";
					$buffer .= $pos;
					$buffer .= $this->indent."\t".'<br />'."\n";
					$buffer .= $this->indent.'</div>'."\n";
				}
				
				if ( $neg )
				{
					$buffer .= $this->indent.'<div class="negative_box">'."\n";
					$buffer .= $this->indent."\t".'<br />'."\n";
					$buffer .= $neg;
					$buffer .= $this->indent."\t".'<br />'."\n";
					$buffer .= $this->indent.'</div>'."\n";
				}
				
				if ( $nfo )
				{
					$buffer .= $this->indent.'<div class="note_box">'."\n";
					$buffer .= $this->indent."\t".'<br />'."\n";
					$buffer .= $nfo;
					$buffer .= $this->indent."\t".'<br />'."\n";
					$buffer .= $this->indent.'</div>'."\n";
				}
			}
			else
			{
				$tmp = NULL;
				
				foreach ( $this->notifications as $message )
				{
					if ( $message['type'] == $type )
						$tmp .= "\t".$this->notification_to_html($message);
				}
				
				if ( $tmp )
				{
					switch ( $type )
					{
						case _MSG_SUCCESS :
							$buffer .= $this->indent.'<div class="positive_box">'."\n";
							$buffer .= $this->indent."\t".'<br />'."\n";
							$buffer .= $tmp;
							$buffer .= $this->indent."\t".'<br />'."\n";
							$buffer .= $this->indent.'</div>'."\n";
							break;
						
						case _MSG_ERROR :
							$buffer .= $this->indent.'<div class="negative_box">'."\n";
							$buffer .= $this->indent."\t".'<br />'."\n";
							$buffer .= $tmp;
							$buffer .= $this->indent."\t".'<br />'."\n";
							$buffer .= $this->indent.'</div>'."\n";
							break;
						
						case _MSG_INFO :
							$buffer .= $this->indent.'<div class="note_box">'."\n";
							$buffer .= $this->indent."\t".'<br />'."\n";
							$buffer .= $tmp;
							$buffer .= $this->indent."\t".'<br />'."\n";
							$buffer .= $this->indent.'</div>'."\n";
							break;
						
						default :
							$buffer .= $tmp;
							break;
					}
				}
			}
			
			return $buffer;
		}
		
		/* Display the generic warning message to alert people
		that the site is not working properly. */
		public function display_warning ()
		{
			$this->notification(1, _WARN_MSG);
		}
		
		public function flush_messages ()
		{
			$this->messages = array();
		}
		
		public function flush_notifications ()
		{
			$this->notifications = array();
		}
		
		function __destruct ()
		{			
			/* Check for writting logs */
			if (_SAVE_LOG)
				$this->write_log_file();
			
			$this->flush_messages();
			$this->flush_notifications();
		}
	}
	
?>
