<?php
	
	/*
		CLASS Database handler - v1.31 (24/03/2010)
		Author : LondNoir (londnoir@sdmedia.be)
		
		mysql/mysqli class handler.
		
		|Changelog - 1.31|
		- dbaccess::check_link() try to reconnect the db

		NOTE : Need mBus class to word properly.
		
		=============================================
		|| DEFINES required by the class,          ||
		|| these must be defined in a config file. ||
		=============================================
		
		define('_DB_HOST', 'localhost');
		define('_DB_USER', '');
		define('_DB_PASS', '');
		define('_DB_NAME', 'dbname');
		define('_DB_TYPE', 'mysqli');
	*/
	
	class dbaccess
	{
		private $host = NULL;
		private $user = NULL;
		private $pass = NULL;
		
		private $dbType = _DB_TYPE;
		private $db = NULL;
		private $link = NULL;
		
		private $sql = NULL;
		private $count = 0;
		private $insertID = 0;
		
		private $lastQueryStatus = TRUE;
		private $noConnexion = FALSE;
		
		private $lastCheckTS = 0;
		
		private $mBus;
		
		function __construct (mbus $mBus, $db = _DB_NAME, $user = _DB_USER, $pass = _DB_PASS, $host = _DB_HOST)
		{
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->db = $db;
			
			$this->mBus = $mBus;
			
			switch ( $this->dbType )
			{
				case 'mysql' :
					return $this->connexion_mysql();
					break;
					
				case 'mysqli' :
					return $this->connexion_mysqli();
					break;
				
				default :
					$this->mBus->add(_MSG_ERROR, 'Le type de base données n\'est pas définit!', 'dbaccess::_construct()');
					$this->mBus->display_warning();
					break;
			}
			
			return FALSE;
		}
		
		private function connexion_mysql ()
		{
			/* Connexion to database with mysqli module */
			if ( $this->link = @mysql_connect($this->host, $this->user, $this->pass, $this->link) )
			{
				/* Table select */
				if ( !mysql_select_db($this->db, $this->link) )
				{
					$this->mBus->add(_MSG_ERROR, 'dbaccess::connexion_mysqli()', 'La base `'.$this->db.'` n\'est pas accessible!', 'dbaccess::connexion_mysql()');
					$this->mBus->display_warning();
				}
				
				/* Charset select */
				if ( !mysql_set_charset('utf8', $this->link) )
				{
					$this->mBus->add(_MSG_ERROR, 'La base `'.$this->db.'` n\'est pas accessible en UTF-8!', 'dbaccess::connexion_mysql()');
					$this->mBus->display_warning();
				}
				
				$this->lastCheckTS = time();
				
				return TRUE;
			}
			else
			{
				$this->mBus->add(_MSG_ERROR, 'La base `'.$this->db.'` n\'est pas accessible!', 'dbaccess::connexion_mysql()');
				$this->mBus->display_warning();
				
				$this->noConnexion = TRUE;
			}
			
			return FALSE;
		}
		
		private function connexion_mysqli ()
		{
			/* Connexion to database with mysqli module */
			if ( $this->link = @mysqli_connect($this->host, $this->user, $this->pass, $this->db) )
			{
				/* Charset select */
				if ( !mysqli_set_charset($this->link, 'utf8') )
				{
					$this->mBus->add(_MSG_ERROR, 'La base `'.$this->db.'` n\'est pas accessible en UTF-8!', 'dbaccess::connexion_mysqli()');
					$this->mBus->display_warning();
				}
				
				$this->lastCheckTS = time();
				
				return TRUE;
			}
			else
			{
				$this->mBus->add(_MSG_ERROR, 'La base `'.$this->db.'` n\'est pas accessible!', 'dbaccess::connexion_mysqli()');
				$this->mBus->display_warning();
				
				$this->noConnexion = TRUE;
			}
			
			return FALSE;
		}
		
		public function change_db ($newDB)
		{
			$result = TRUE;
			
			if ( $this->check_link() )
			{
				switch ( $this->dbType )
				{
					case 'mysql' :
						if ( !mysql_select_db($newDB, $this->link) )
						{
							$result = FALSE;
						}
						break;
						
					case 'mysqli' :
						if ( !mysqli_select_db($this->link, $newDB) )
						{
							$result = FALSE;
						}
						break;
						
					default :
						$result = FALSE;
						$this->mBus->add(_MSG_ERROR, 'Mauvais type de base de données', 'dbaccess::change_db()');
						break;
				}
				
				if ( !$result )
				{
					$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::change_db()');
				}
			}
			else
			{
				$result = FALSE;
			}
			
			return $result;
		}
		
		public function get_link ()
		{
			return $this->link;
		}
		
		private function check_link ()
		{
			/* The server has never been reached! */
			if ( $this->noConnexion )
				return FALSE;
			
			/* Avoid to check too many time. */
			if ( time() - $this->lastCheckTS < 300 )
				return TRUE;
			
			$result = FALSE;
			switch ( $this->dbType )
			{
				case 'mysql' :
					if ( mysql_ping($this->link) )
						$result = TRUE;
					else
						$result = $this->connexion_mysql();
					break;
				
				case 'mysqli' :
					if ( mysqli_ping($this->link) )
						$result = TRUE;
					else
						$result = $this->connexion_mysqli();
					break;
			}
			
			if ( $result )
			{
				$this->lastCheckTS = time();
				
				return TRUE;
			}
			else
			{
				$this->mBus->add(_MSG_ERROR, 'database connexion closed!', 'dbaccess::check_link()');
				$this->mBus->display_warning();
			
				$this->lastQueryState = FALSE;
				$this->noConnexion = TRUE;
			
				return FALSE;
			}
		}
		
		private function db_response ()
		{
			switch ( $this->dbType )
			{
				case 'mysql' :
					return 'Request : '.$this->sql."\n".'Response : '.mysql_error($this->link);
					break;
					
				case 'mysqli' :
					return 'Request : '.$this->sql."\n".'Response : '.mysqli_error($this->link);
					break;
					
				default :
					return NULL;
					break;
			}
		}
		
		/* Return a bool for the last query. This can be useful
		for discarding a negative value from db failure. */
		public function get_sql_status ()
		{
			return $this->lastQueryStatus;
		}
		
		/* Return the last SQL */
		public function get_sql ()
		{
			return $this->sql;
		}
		
		/* Return the number of hit records in db with the last sql */
		public function get_count ()
		{
			return $this->count;
		}
		
		/* Return the inserted ID */
		public function get_insert_id ()
		{
			return $this->insertID;
		}
		
		public function table_exists ($table)
		{
			$this->sql = 'SHOW TABLES FROM `'.$this->db.'` LIKE \''.$table.'\';';
			$this->count = 0;
			$this->insertID = 0;
			
			if ( $this->check_link() )
			{
				switch ( $this->dbType )
				{
					case 'mysql' :
						if ( $result = mysql_query($this->sql, $this->link) )
						{
							$this->lastQueryState = TRUE;
					
							$num = mysql_num_rows($result);
							mysql_free_result($result);
					
							return $num;
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::table_exists()');
							$this->mBus->display_warning();
						}
						break;
						
					case 'mysqli' :
						if ( $result = mysqli_query($this->link, $this->sql) )
						{
							$this->lastQueryState = TRUE;
					
							$num = mysqli_num_rows($result);
							mysqli_free_result($result);
					
							return $num;
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::table_exists()');
							$this->mBus->display_warning();
						}
						break;
						
					default :
						break;
				}
			}
			
			return FALSE;
		}
		
		public function execute ($sql)
		{
			$this->sql = $sql;
			$this->count = 0;
			$this->insertID = 0;
			
			if ( $this->check_link() )
			{
				switch ( $this->dbType )
				{
					case 'mysql' :
						if ( mysql_query($sql, $this->link) )
						{
							$this->lastQueryState = TRUE;
							
							$this->count = mysql_affected_rows($this->link);
							$this->insertID = mysql_insert_id($this->link);
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::execute()');
							$this->mBus->display_warning();
						}
						break;
						
					case 'mysqli' :
						if ( mysqli_query($this->link, $sql) )
						{
							$this->lastQueryState = TRUE;
							
							$this->count = mysqli_affected_rows($this->link);
							$this->insertID = mysqli_insert_id($this->link);
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::execute()');
							$this->mBus->display_warning();
						}
						break;
						
					default :
						break;
				}
			}
			else
			{
				return FALSE;
			}
			
			return $this->lastQueryState;
		}
		
		public function get_result ($sql, $type = 'string')
		{
			$this->sql = $sql;
			$this->count = 0;
			$this->insertID = 0;
			
			$dbResult = NULL;
			if ( $this->check_link() )
			{
				/* Launch the query, and wait for response */
				switch ( $this->dbType )
				{
					case 'mysql' :
						if ( $result = mysql_query($this->sql, $this->link) )
						{
							$this->lastQueryState = TRUE;
						
							if ($this->count = mysql_num_rows($result))
							{
								$tmp = mysql_fetch_row($result);
							
								$dbResult = $tmp[0];
							}
							
							mysql_free_result($result);
						}
						else
						{
							$this->lastQueryState = FALSE;
						
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::get_result()');
							$this->mBus->display_warning();
						}
						break;
						
					case 'mysqli' :
						if ( $result = mysqli_query($this->link, $this->sql) )
						{
							$this->lastQueryState = TRUE;
						
							if ($this->count = mysqli_num_rows($result))
							{
								$tmp = mysqli_fetch_row($result);
							
								$dbResult = $tmp[0];
							}
							
							mysqli_free_result($result);
						}
						else
						{
							$this->lastQueryState = FALSE;
						
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::get_result()');
							$this->mBus->display_warning();
						}
						break;
						
					default :
						$dbResult = NULL;
						break;
				}
			}
			else
			{
				return FALSE;
			}
			
			/* examination from the db */
			if ( $this->lastQueryState )
			{
				switch ( $type )
				{
					case 'int' :
						return intval($dbResult);
						break;
						
					case 'float' :
						return floatval($dbResult);
						break;
						
					case 'string' :
					default :
						return $dbResult;
						break;
				}
			}
			else
			{
				return FALSE;
			}
		}
		
		public function get_row ($sql, $mode = 1)
		{
			$this->sql = $sql;
			$this->count = 0;
			$this->insertID = 0;
			
			$array = array();
			if ( $this->check_link() )
			{
				switch ( $this->dbType )
				{
					case 'mysql' :
						if ( $result = mysql_query($this->sql, $this->link) )
						{
							$this->lastQueryState = TRUE;
							
							if ( $this->count = mysql_num_rows($result) )
							{
								switch ($mode)
								{
									case 0 :
										//$array = mysql_fetch_array($result, MYSQLI_NUM);
										$array = mysql_fetch_row($result);
										break;
									
									case 1 :
										//$array = mysql_fetch_array($result, MYSQLI_ASSOC);
										$array = mysql_fetch_assoc($result);
										break;
									
									case 2 :
										$array = mysql_fetch_array($result, MYSQLI_BOTH);
										break;
									
									default :
										$this->mBus->add(_MSG_ERROR, 'PARAM ERROR -> mode = '.$mode, 'dbaccess::get_row()');
										$this->mBus->display_warning();
										$array = FALSE;
										break;
								}
								
								mysql_free_result($result);
								
								return $array;
							}
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::get_row()');
							$this->mBus->display_warning();
						}
						break;
						
					case 'mysqli' :
						if ( $result = mysqli_query($this->link, $this->sql) )
						{
							$this->lastQueryState = TRUE;
							
							if ( $this->count = mysqli_num_rows($result) )
							{
								switch ( $mode )
								{
									case 0 :
										//$array = mysqli_fetch_array($result, MYSQLI_NUM);
										$array = mysqli_fetch_row($result);
										break;
									
									case 1 :
										//$array = mysqli_fetch_array($result, MYSQLI_ASSOC);
										$array = mysqli_fetch_assoc($result);
										break;
									
									case 2 :
										$array = mysqli_fetch_array($result, MYSQLI_BOTH);
										break;
									
									default :
										$this->mBus->add(_MSG_ERROR, 'PARAM ERROR -> mode = '.$mode, 'dbaccess::get_row()');
										$this->mBus->display_warning();
										$array = FALSE;
										break;
								}
								
								mysqli_free_result($result);
								
								return $array;
							}
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::get_row()');
							$this->mBus->display_warning();
						}
						break;
						
					default :
						break;
				}
			}
			
			return FALSE;
		}
		
		public function get_array ($sql, $mode = 1)
		{
			$this->sql = $sql;
			$this->count = 0;
			$this->insertID = 0;
			
			$array = array();
			if ( $this->check_link() )
			{
				switch ( $this->dbType )
				{
					case 'mysql' :
						if ( $result = mysql_query($this->sql, $this->link) )
						{
							$this->lastQueryState = TRUE;
							
							if ( $this->count = mysql_num_rows($result) )
							{
								switch ($mode)
								{
									case 0 :
										//while ($row = mysql_fetch_array($result, MYSQLI_NUM))
										while ($row = mysql_fetch_row($result))
											$array[] = $row;
										break;
									
									case 1 :
										//while ($row = mysql_fetch_array($result, MYSQLI_ASSOC))
										while ($row = mysql_fetch_assoc($result))
											$array[] = $row;
										break;
									
									case 2 :
										while ($row = mysql_fetch_array($result, MYSQLI_BOTH))
											$array[] = $row;
										break;
									
									default :
										$this->mBus->add(_MSG_ERROR, 'PARAM ERROR -> mode = '.$mode, 'dbaccess::get_array()');
										$this->mBus->display_warning();
										$array = FALSE;
										break;
								}
								
								mysql_free_result($result);
								
								return $array;
							}
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::get_array()');
							$this->mBus->display_warning();
						}
						break;
						
					case 'mysqli' :
						if ( $result = mysqli_query($this->link, $this->sql) )
						{
							$this->lastQueryState = TRUE;
							
							if ( $this->count = mysqli_num_rows($result) )
							{
								switch ($mode)
								{
									case 0 :
										//while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
										while ( $row = mysqli_fetch_row($result) )
											$array[] = $row;
										break;
									
									case 1 :
										//while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
										while ( $row = mysqli_fetch_assoc($result) )
											$array[] = $row;
										break;
									
									case 2 :
										while ( $row = mysqli_fetch_array($result, MYSQLI_BOTH) )
											$array[] = $row;
										break;
									
									default :
										$this->mBus->add(_MSG_ERROR, 'PARAM ERROR -> mode = '.$mode, 'dbaccess::get_array()');
										$this->mBus->display_warning();
										$array = FALSE;
										break;
								}
								
								mysqli_free_result($result);
								
								return $array;
							}
						}
						else
						{
							$this->lastQueryState = FALSE;
							
							$this->mBus->add(_MSG_ERROR, $this->db_response(), 'dbaccess::get_array()');
							$this->mBus->display_warning();
						}
						break;
						
					default :
						break;
				}
			}
			
			return FALSE;
		}
		
		function write_sql ($table, $vars, $id = 0)
		{
			$sql = NULL;
		
			if ( is_array($vars) && count($vars) )
			{	
				if ( $id )
				{
					$sql .= 'UPDATE `'.$table.'` SET ';
					$tmp = array();
					foreach ( $vars as $field => $value )
						$tmp[] = '`'.$field.'` = '.( (is_int($value) || is_float($value)) ? $value : '\''.$value.'\'');
					
					$sql .= implode(', ', $tmp).' ';
					$sql .= 'WHERE `id` = '.$id.' LIMIT 1;';
				}
				else
				{
					$sql .= 'INSERT INTO `'.$table.'` ';
					
					$tmpA = array();
					$tmpB = array();
					foreach ( $vars as $field => $value )
					{
						$tmpA[] = '`'.$field.'`';
						if ( is_int($value) || is_float($value) )
							$tmpB[] = $value;
						else
							$tmpB[] = '\''.$value.'\'';
					}
					
					$sql .= '('.implode(', ', $tmpA).') ';
					$sql .= 'VALUES ('.implode(', ', $tmpB).');';
				}
			}
			
			$this->sql = $sql;
			
			return $sql;
		}
		
		public function safe_string ($string)
		{
			switch ( $this->dbType )
			{
				case 'mysql' :
					return mysql_real_escape_string($string, $this->link);
					break;
					
				case 'mysqli' :
					return mysqli_real_escape_string($this->link, $string);
					break;
					
				/* Do nothing*/
				default :
					return $string;
					break;
			}
		}
		
		/* CHECK */
		function __destruct ()
		{
			switch ($this->dbType)
			{
				case 'mysql' :
					if ( $this->link )
						@mysql_close($this->link);
					break;
					
				case 'mysqli' :
					if ( $this->link )
						@mysqli_close($this->link);
					break;
					
				default :
					break;
			}
		}
	}
	
?>
