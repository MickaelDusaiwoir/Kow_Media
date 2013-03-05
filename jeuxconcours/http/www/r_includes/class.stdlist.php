<?php
	
	/*
		CLASS StdList - v1.0 (12/05/2010)
		Author : LondNoir (londnoir@sdmedia.be)
		
		Standard list class.
		
	*/
	
	class stdlist
	{
		private $table = NULL;
		private $selectFields = array();
		private $sqlWhere = NULL;
		private $orders = array();
		private $byRowSQL = NULL;
		
		private $page = 1;
		private $pagesCount = 1;
		private $displayLimit = 30;
		
		private $title = NULL;
		private $currentURL = NULL;
		private $indent = "\t\t\t\t";
		private $columns = array();
		
		private $mBus = NULL;
		private $db = NULL;
		
		function __construct (mbus $mBus, dbaccess $db, $table = NULL)
		{
			$this->mBus = $mBus;
			$this->db = $db;
			
			if ( $table )
				$this->table = $table;
			
			$this->currentURL = _SITE_URL.substr($_SERVER['SCRIPT_NAME'], 1);
			
			$this->page = get_value('page', 'gp', 'uint', 1);
		}
		
		private function compute ()
		{
			if ( $elementsCount = $this->db->get_result('SELECT COUNT(1) FROM `'.$this->table.'`'.$this->sqlWhere.';', 'int') )
			{
				$this->pagesCount = ceil($elementsCount / $this->displayLimit);
				
				if ( $this->page > $this->pagesCount )
					$this->page = $this->pagesCount;
			}
			
			return $elementsCount;
		}
		
		private function get_sql ()
		{
			$sql = NULL;
			
			if ( empty($this->selectFields) )
			{
				$this->mBus->add(_MSG_ERROR, 'Les données à rechercher ne sont pas spécifiées !', 'stdlist::get_sql()');
				
				return NULL;
			}
					
			/* SELECT Clause */
			$tmp = array();
			foreach ( $this->selectFields as $field )
				$tmp[] = ' `'.$field.'` ';
			$sql .= 'SELECT '.implode(',', $tmp).' FROM `'.$this->table.'`';
			
			/* WHERE Clause */
			$sql .= $this->sqlWhere;
			
			/* ORDER Clause */
			if ( $this->orders )
			{
				$tmp = array();
				
				foreach ( $this->orders as $order )
					$tmp[] = ' `'.$order['name'].'` '.$order['dir'].' ';
				
				$sql .= ' ORDER BY '.implode(',', $tmp);
			}
			
			/* LIMIT Clause */
			$sql .= ' LIMIT '.( ($this->page - 1) * $this->displayLimit ).', '.( $this->displayLimit ).';';
			
			return $sql;
		}
		
		public function set_table ($table)
		{
			$this->table = $table;
		}
		
		public function set_select_fields (array $sFields)
		{
			$this->selectFields = $sFields;
		}
		
		public function data_query_by_row ($sql)
		{
			$this->byRowSQL = $sql;
		}
		
		public function add_column ($title, $size = 64, $isRight = FALSE)
		{
			$this->columns[] = array(
				'title' => $title,
				'data' => array(),
				'size' => max(8, intval($size)),
				'is_right' => $isRight ? TRUE : FALSE
			);
		}
		
		public function add_quick_column ($title, $fieldname, $size = 64, $isRight = FALSE)
		{
			$this->columns[] = array(
				'title' => $title,
				'data' => array(
					0 => array(
						'type' => 0,
						'special' => $fieldname
					),
				),
				'size' => max(8, intval($size)),
				'is_right' => $isRight ? TRUE : FALSE
			);
		}
		
		public function set_column_data ($fieldname)
		{
			end($this->columns);
			$key = key($this->columns);
			
			$this->columns[$key]['data'][] = array(
				'type' => 0,
				'special' => $fieldname
			); 
		}
		
		public function set_column_parsed_data ($rules)
		{
			end($this->columns);
			$key = key($this->columns);
			
			$this->columns[$key]['data'][] = array(
				'type' => 1,
				'special' => $rules
			); 
		}
		
		public function set_column_condition_data (column_options $options)
		{
			end($this->columns);
			$key = key($this->columns);
			
			$this->columns[$key]['data'][] = array(
				'type' => 2,
				'special' => $options->get_options()
			); 
		}

		public function set_column_query_data ($sql, $rowDataType = 'string')
		{
			end($this->columns);
			$key = key($this->columns);

			$this->columns[$key]['data'][] = array(
				'type' => 3,
				'special' => $sql,
				'data_type' => $rowDataType
			); 
		}
		
		public function set_sql_where ($sqlWhere)
		{
			$this->sqlWhere = 'WHERE '.$sqlWhere.' ';
		}
		
		public function set_order ($fieldname, $asc = FALSE)
		{
			$this->orders[] = array(
				'name' => $fieldname,
				'dir' => $asc ? 'ASC' : 'DESC'
			);
		}
		
		public function set_limit ($displayLimit)
		{
			$displayLimit = intval($displayLimit);
			
			if ( $displayLimit > 0 )
				$this->displayLimit = $displayLimit;
		}
		
		public function set_title ($title)
		{
			$this->title = $title;
		}
		
		private function apply_data_on_layout (array $data, $layout, array $rowSpecificData = NULL)
		{
			$in = array();
			$out = array();
			
			$matches = array();
			if ( preg_match_all('#\{([0-9a-wA-W-_]{1,64})\}#', $layout, $matches) )
			{
				foreach ( $matches[1] as $match )
				{
					/* Avoid to call something wich is not in fields array */
					if ( isset($data[ $match ]) )
					{
						/* Avoid duplicated fieldname */
						if ( !in_array('{'.$match.'}', $in) )
						{
							$in[] = '{'.$match.'}';
							$out[] = $data[ $match ];
						}
					}
					else
					{
						$this->mBus->add(_MSG_ERROR, 'Recherche de la valeur "'.$match.'" ne se trouvant pas dans le resultat de la requête SQL !');
					}
				}
			}

			if ( $rowSpecificData )
			{
				$matches = array();
				if ( preg_match_all('#\{\*([0-9a-wA-W-_]{1,64})\}#', $layout, $matches) )
				{
					foreach ( $matches[1] as $match )
					{
						/* Avoid to call something wich is not in fields array */
						if ( isset($rowSpecificData[ $match ]) )
						{
							/* Avoid duplicated fieldname */
							if ( !in_array('{*'.$match.'}', $in) )
							{
								$in[] = '{*'.$match.'}';
								$out[] = $rowSpecificData[ $match ];
							}
						}
						else
						{
							$this->mBus->add(_MSG_ERROR, 'Recherche de la valeur "'.$match.'" ne se trouvant pas dans le resultat de la requête SQL par ligne !');
						}
					}
				}
			}
			
			/* Replace tags */
			if ( !empty($in) && !empty($out) )
				$layout = str_replace($in, $out, $layout);
			
			return $layout;
		}

		/* Check if the key called is the main data or row data request. */
		private function is_row_data ($key)
		{
			if ( $key[0] == '*' )
				return substr($key, 1);
			else
				return NULL;
		}
		
		public function get_html ()
		{
			if ( !$this->table )
			{
				$this->mBus->add(_MSG_ERROR, 'La table n\'est pas spécifiée !', 'stdlist::get_html_list()');
				return NULL;
			}
			
			$this->compute();
			
			if ( !$sql = $this->get_sql() )
			{
				$this->mBus->add(_MSG_ERROR, 'La requête SQL est null !', 'stdlist::get_html_list()');
				
				return NULL;
			}
			
			$HTMLContent = $this->indent.'<div class="list">'."\n";
			if ( $this->title )
				$HTMLContent .= $this->indent.'<h2>'.$this->title.'</h2>'."\n";
			if ( $rowDatas = $this->db->get_array($sql) )
			{
				# stdlist toolsbox
				$HTMLContent .= $this->indent."\t".'<p class="list_navbox"> Page '.$this->page.' / '.$this->pagesCount.' '."\n";
				if ( $this->pagesCount > 1 )
				{
					if ( $this->page > 1 )
					{
						$HTMLContent .=  $this->indent."\t\t".'<a href="'.$this->currentURL.'?page=1"> <img src="'._SITE_URL.'css/control_fplay.png" alt="Première page" /> </a>'."\n";
						$HTMLContent .=  $this->indent."\t\t".'<a href="'.$this->currentURL.'?page='.( $this->page - 1 ).'"> <img src="'._SITE_URL.'css/control_play.png" alt="Page précédente" /> </a>'."\n";
					}
			
					if ( $this->page < $this->pagesCount )
					{
						$HTMLContent .=  $this->indent."\t\t".'<a href="'.$this->currentURL.'?page='.( $this->page + 1 ).'"> <img src="'._SITE_URL.'css/control_play2.png" alt="Page suivante" /> </a>'."\n";
						$HTMLContent .=  $this->indent."\t\t".'<a href="'.$this->currentURL.'?page='.$this->pagesCount.'"> <img src="'._SITE_URL.'css/control_fplay2.png" alt="Dernière page" /> </a>'."\n";
					}
				}
				$HTMLContent .= $this->indent."\t".'</p>'."\n";
		
				# stdlist header
				$HTMLContent .= $this->indent."\t".'<div class="list_hrow">'."\n";
				foreach ( $this->columns as $column )
				{
					$HTMLContent .= $this->indent."\t\t".'<div class="list_hcase'.( $column['is_right'] ? '_r' : NULL ).'" style="width:'.$column['size'].'px">'."\n";
					$HTMLContent .= $this->indent."\t\t\t".'<p class="list_htext">'.$column['title'].'</p>'."\n";
					$HTMLContent .= $this->indent."\t\t".'</div>'."\n";
				}
				$HTMLContent .= $this->indent."\t\t".'<div class="float_breaker"></div>'."\n";
				$HTMLContent .= $this->indent."\t".'</div>'."\n";
		
				# stdlist body
				foreach ( $rowDatas as $rowData )
				{
					$extRowData = array();
					if ( $this->byRowSQL != NULL )
					{
						$parsedSQL = $this->apply_data_on_layout($rowData, $this->byRowSQL);
						$extRowData = $this->db->get_row($parsedSQL);
					}
					
					$HTMLContent .= $this->indent."\t".'<div class="list_row">'."\n";
					foreach ( $this->columns as $column )
					{
						$displayResult = NULL;
						foreach ( $column['data'] as $data )
						{
							switch ( $data['type'] )
							{
								/* Display directly data from db */
								case 0 :
									if ( $realKey = $this->is_row_data($data['special']) )
										$displayResult .= isset($extRowData[$realKey]) ? $extRowData[$realKey] : 'na';
									else
										$displayResult .= isset($rowData[ $data['special'] ]) ? $rowData[ $data['special'] ] : 'na';
									break;

								/* Parse a string with data from db, following {key} or {*key} */
								case 1 :
									$add = $this->is_row_data($data['special']) ? $extRowData : NULL;
									$displayResult .= $this->apply_data_on_layout($rowData, $data['special'], $add);
									break;

								/* Parse a string from array of possibilities,
								that meet the condition. */
								case 2 :
									$fieldname = $data['special'][0];
									$options = $data['special'][1];
									
									if ( isset($rowData[$fieldname]) )
									{
										foreach ( $options as $condition => $solution )
										{
											if ( $condition == $rowData[$fieldname] )
											{
												/* Check if the result request a parsing or not. */
												if ( $solution[1] )
												{
													$add = $this->is_row_data($solution[0]) ? $extRowData : NULL;
													$displayResult .= $this->apply_data_on_layout($rowData, $solution[0], $add);
												}
												else
												{
													if ( $realKey = $this->is_row_data($solution[0]) )
														$displayResult .= isset($extRowData[$realKey]) ? $extRowData[$realKey] : 'na';
													else
														$displayResult .= isset($rowData[ $solution[0] ]) ? $rowData[ $solution[0] ] : 'na';
												}
												break;
											}
										}
									}
									else
									{
										$displayResult = '_error';
									}
									break;

								/* Display the result of a simple parsed SQL request. */
								case 3 :
									$parsedSQL = $this->apply_data_on_layout($rowData, $data['special']);
									$displayResult .= $this->db->get_result($parsedSQL, $data['data_type']);
									break;
							}
						}
						
						$HTMLContent .= $this->indent."\t\t".'<div class="list_case'.( $column['is_right'] ? '_r' : NULL ).'" style="width:'.$column['size'].'px">'."\n";
						$HTMLContent .= $this->indent."\t\t\t".'<p class="list_text">'.$displayResult.'</p>'."\n";
						$HTMLContent .= $this->indent."\t\t".'</div>'."\n";
					}
					$HTMLContent .= $this->indent."\t\t".'<div class="float_breaker"></div>'."\n";
					$HTMLContent .= $this->indent."\t".'</div>'."\n";
				}
			}
			else
			{
				$HTMLContent .= $this->indent."\t".'<p class="message_note">Il n\'y a pas d\'élement dans cette liste.</p>'."\n";
			}
			$HTMLContent .= $this->indent.'</div>'."\n";
			
			return $HTMLContent;
		}
		
		function __destruct ()
		{
			
		}
	}
	
	/* Build up a valid array for column definitions */
	class column_options
	{
		private $fieldname = NULL;
		private $options = array();
		
		function __construct ($fieldname)
		{
			$this->fieldname = $fieldname;
		}
		
		public function add_option ($value, $display)
		{
			$requestParsing = strpos($display, '{') ? TRUE : FALSE;
			$this->options[$value] = array($display, $requestParsing);
		}
		
		public function clear ($fieldname)
		{
			$this->fieldname = $fieldname;
			$this->options = array();
		}
		
		public function get_options ()
		{
			return array($this->fieldname, $this->options);
		}
		
		function __destruct ()
		{
			$this->clear(NULL);
		}
	}
	
?>
