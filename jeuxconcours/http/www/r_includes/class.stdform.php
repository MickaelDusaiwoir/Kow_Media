<?php
	
	/*
		CLASS StdForm - v1.0 (20/04/2010)
		Author : LondNoir (londnoir@sdmedia.be)
		
		Standard form class.
		
	*/
	
	class stdform
	{
		private $formID = NULL;
		private $goto = NULL;
		private $method = 'post';

		private $fields = array();
		
		/* Customization */
		private $style = NULL;
		private $title = NULL;
		private $additionals = NULL;
		private $sendText = 'Envoyer';
		
		function __construct ($formID = NULL)
		{
			$this->formID = $formID; 
		}

		public function set_action ($url)
		{
			$this->goto = $url;
		}

		public function set_method ($method)
		{
			switch ( strtolower($method) )
			{
				case 'get' :
					$this->method = 'get';
					break;
					
				case 'post' :
				default :
					$this->method = 'post';
					break;				
			}
		}
		
		public function set_style ($css)
		{
			$this->style = ' style="'.$css.'"';
		}
		
		/* This function will add specific css on the last field added. */
		public function set_field_style ($css)
		{
			if ( $this->fields )
			{
				end($this->fields);
				$key = key($this->fields);
				$this->fields[$key]['style'] = ' style="'.$css.'"';
			}
		}

		public function add_text_field ($name, $label, $value = NULL)
		{
			$this->fields[] = array(
				'type' => 'text',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => NULL,
				'style' => NULL,
				'value' => $value
			);
		}

		public function add_password_field ($name, $label, $value = NULL)
		{
			$this->fields[] = array(
				'type' => 'password',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => NULL,
				'style' => NULL,
				'value' => $value
			);
		}

		public function add_hidden_field ($name, $value = NULL)
		{
			$this->fields[] = array(
				'type' => 'hidden',
				'id' => NULL,
				'name' => $name,
				'label' => NULL,
				'options' => NULL,
				'style' => NULL,
				'value' => $value
			);
		}

		public function add_textarea_field ($name, $label, $value = NULL)
		{
			$this->fields[] = array(
				'type' => 'textarea',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => NULL,
				'style' => NULL,
				'value' => $value
			);
		}

		public function add_checkbox_field ($name, $label, $value = FALSE)
		{
			$this->fields[] = array(
				'type' => 'checkbox',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => NULL,
				'style' => NULL,
				'value' => $value
			);
		}

		public function add_select_field ($name, $label, array $options, $value = NULL)
		{
			$this->fields[] = array(
				'type' => 'select',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => $options,
				'style' => NULL,
				'value' => $value
			);
		}
		
		public function add_multiselect_field ($name, $label, array $options, $value = NULL)
		{
			$this->fields[] = array(
				'type' => 'multiselect',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => $options,
				'style' => NULL,
				'value' => $value
			);
		}

		public function add_radio_field ($name, $label, array $options, $default = NULL)
		{
			$this->fields[] = array(
				'type' => 'radio',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => $options,
				'style' => NULL,
				'value' => $default
			);
		}
		
		public function add_date_field ($name, $label, array $defaults)
		{
			$this->fields[] = array(
				'type' => 'date',
				'id' => $this->formID ? $this->formID.'_'.$name : $name,
				'name' => $name,
				'label' => $label,
				'options' => NULL,
				'style' => NULL,
				'value' => $defaults
			);
		}
		
		public function set_title ($value)
		{
			$this->title = $value;
		}
		
		public function add_html ($value)
		{
			$this->additionals = $value;
		}
		
		public function send_button_value ($value)
		{
			$this->sendText = $value;
		}
		
		public function get_html ()
		{
			$buffer = NULL;

			/* Hidden fields are groupped with submit button div */
			$hiddens = NULL;

			if ( empty($this->goto) )
				$this->goto = _SITE_URL.substr($_SERVER['SCRIPT_NAME'], 1);

			$buffer .= "\t\t\t\t".'<div class="form"'.( $this->style ? $this->style : NULL ).'>'."\n";
			if ( $this->title )
				$buffer .= "\t\t\t\t\t".'<h2 class="form">'.$this->title.'</h2>'."\n";
			if ( $this->additionals )
				$buffer .= $this->additionals;
			$buffer .= "\t\t\t\t\t".'<form'.( $this->formID ? ' id="'.$this->formID.'"' : NULL ).' action="'.$this->goto.'" method="'.$this->method.'">'."\n";

			foreach ( $this->fields as $field )
			{
				if ( $field['type'] == 'hidden')
				{
					$hiddens .= "\t\t\t\t\t\t\t".'<input type="hidden" name="'.$field['name'].'" value="'.$field['value'].'" />'."\n";
				}
				else
				{
					$buffer .= "\t\t\t\t\t\t".'<div class="form_section">'."\n";
					if ( $field['label'] )
					{
						$buffer .= "\t\t\t\t\t\t\t".'<label class="form" for="'.$field['id'].'">'.$field['label'].'</label>'."\n";
					}
					switch ( $field['type'] )
					{
						case 'text' :
							$buffer .= "\t\t\t\t\t\t\t".'<input class="form"'.( $field['style'] ? $field['style'] : NULL ).' type="text" id="'.$field['id'].'" name="'.$field['name'].'" value="'.$field['value'].'" />'."\n";
							break;

						case 'password' :
							$buffer .= "\t\t\t\t\t\t\t".'<input class="form"'.( $field['style'] ? $field['style'] : NULL ).' type="password" id="'.$field['id'].'" name="'.$field['name'].'" value="'.$field['value'].'" />'."\n";
							break;

						case 'hidden' :
							/* Should never be here! */
							break;
						
						case 'textarea' :
							$buffer .= "\t\t\t\t\t\t\t".'<textarea class="form"'.( $field['style'] ? $field['style'] : NULL ).' id="'.$field['id'].'" name="'.$field['name'].'">'.$field['value'].'</textarea>'."\n";
							break;
						
						case 'checkbox' :
							$buffer .= "\t\t\t\t\t\t\t".'<input class="form"'.( $field['style'] ? $field['style'] : NULL ).' type="checkbox" id="'.$field['id'].'" name="'.$field['name'].'" '.( $field['value'] ? 'checked="checked" ' : NULL ).'/>'."\n";
							break;
						
						case 'select' :
							$buffer .= "\t\t\t\t\t\t\t".'<select class="form"'.( $field['style'] ? $field['style'] : NULL ).' id="'.$field['id'].'" name="'.$field['name'].'">'."\n";
							foreach ( $field['options'] as $value => $option )
								$buffer .= "\t\t\t\t\t\t\t\t".'<option value="'.$value.'"'.( ($field['value'] == $value) ? ' selected="selected"' : NULL ).'>'.$option.'</option>'."\n";
							$buffer .= "\t\t\t\t\t\t\t".'</select>'."\n";
							break;
							
						case 'multiselect' :
							$buffer .= "\t\t\t\t\t\t\t".'<select class="form"'.( $field['style'] ? $field['style'] : NULL ).' id="'.$field['id'].'" name="'.$field['name'].'[]" multiple="multiple">'."\n";
							foreach ( $field['options'] as $value => $option )
								$buffer .= "\t\t\t\t\t\t\t\t".'<option value="'.$value.'"'.( in_array($value, $field['value']) ? ' selected="selected"' : NULL ).'>'.$option.'</option>'."\n";
							$buffer .= "\t\t\t\t\t\t\t".'</select>'."\n";
							break;
						
						case 'radio' :
							$i = 1;
							foreach ( $field['options'] as $value => $option )
							{
								$buffer .= "\t\t\t\t\t\t\t".'<input type="radio"'.( $field['style'] ? $field['style'] : NULL ).' id="'.$field['id'].'_'.$i.'" name="'.$field['name'].'" value="'.$value.'" '.( ($field['value'] == $value) ? 'checked="checked" ' : NULL ).'/>'."\n";
								$buffer .= "\t\t\t\t\t\t\t".'<label for="'.$field['id'].'_'.$i.'">'.$option.'</label>'."\n";
								$i++;
							}
							break;

						case 'date' :
							/* day */
							$buffer .= "\t\t\t\t\t\t\t".'<select class="form"'.( $field['style'] ? $field['style'] : NULL ).' id="'.$field['id'].'_day" name="'.$field['name'].'_day">'."\n";
							for ( $i = 1; $i < 32; $i++ )
								$buffer .= "\t\t\t\t\t\t\t\t".'<option value="'.$i.'"'.( ($field['value']['day'] == $i) ? ' selected="selected"' : NULL ).'>'.$i.'</option>'."\n";
							$buffer .= "\t\t\t\t\t\t\t".'</select>'."\n";
							/* month */
							$buffer .= "\t\t\t\t\t\t\t".'<select class="form"'.( $field['style'] ? $field['style'] : NULL ).' id="'.$field['id'].'_month" name="'.$field['name'].'_month">'."\n";
							for ( $i = 1; $i < 13; $i++ )
								$buffer .= "\t\t\t\t\t\t\t\t".'<option value="'.$i.'"'.( ($field['value']['month'] == $i) ? ' selected="selected"' : NULL ).'>'.$i.'</option>'."\n";
							$buffer .= "\t\t\t\t\t\t\t".'</select>'."\n";
							/* year */
							$buffer .= "\t\t\t\t\t\t\t".'<input class="form"'.( $field['style'] ? $field['style'] : NULL ).' type="text" id="'.$field['id'].'_year" name="'.$field['name'].'_year" value="'.$field['value']['year'].'" />'."\n";
							break;
						
						default :
							break;
					}
					$buffer .= "\t\t\t\t\t\t".'</div>'."\n";
				}
			}

			$buffer .= "\t\t\t\t\t\t".'<br />'."\n";
			$buffer .= "\t\t\t\t\t\t".'<div class="form_section">'."\n";
			if ( $hiddens )
			{
				/* Writes hiddens fields after all other visible fields. */
				$buffer .= $hiddens;
			}
			if ( $this->formID )
				$buffer .= "\t\t\t\t\t\t\t".'<input type="submit" id="'.$this->formID.'_submitter" name="'.$this->formID.'_submitted" value="'.$this->sendText.'" />'."\n";
			else
				$buffer .= "\t\t\t\t\t\t\t".'<input type="submit" id="submitter" name="submitted" value="'.$this->sendText.'" />'."\n";
			$buffer .= "\t\t\t\t\t\t".'</div>'."\n";
			
			$buffer .= "\t\t\t\t\t".'</form>'."\n";
			$buffer .= "\t\t\t\t".'</div>'."\n";

			return $buffer;
		}
		
		function __destruct ()
		{
			
		}
	}
	
?>
