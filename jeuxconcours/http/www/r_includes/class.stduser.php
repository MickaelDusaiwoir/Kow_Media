<?php

	/*
		CLASS User - v1.1 (11/05/2010)
		Author : LondNoir (londnoir@sdmedia.be)
		
		Manage users authentification and rights.

		=============================================
		|| DEFINES required by the class,          ||
		|| these must be defined in a config file. ||
		=============================================
		
		define('USER_DEBUG', FALSE);
		define('USER_TABLE', 'pwm_users');
		define('USER_AUTHKEY_DAYLIFE', 15);
		
	*/
	
	class stduser
	{
		private $mBus = NULL;
		private $db = NULL;
		
		/* auth information */
		private $username = NULL;
		private $password = NULL;
		private $authkey = NULL;
		
		/* Account information */
		private $id = 0;
		private $email = NULL;
		private $level = 0;		
		
		function __construct (mbus $mBus, dbaccess $db)
		{
			$this->mBus = $mBus;
			$this->db = $db;
			
			if ( $tmpKey = $this->search_authkey() )
			{
				/* User knows at least the website... */
				if ( $this->check_authkey_validity($tmpKey) )
				{
					/* ... with a valid authentification key. */
					$this->authkey = $tmpKey;
					
					if ( USER_DEBUG )
						$this->mBus->add(_MSG_SUCCESS, 'authkey found ! '.$this->authkey);
					
					$this->check_authentification();
				}
				else
				{
					if ( USER_DEBUG )
						$this->mBus->add(_MSG_ERROR, 'Bad authkey detected.');
					
					/* Bad authentification key */
					$this->destroy_client_authkey();
				}
			}
			else
			{
				/* User is a visitor ... */
				$this->username = 'visiteur';
			}
		}
		
		private function check_authentification ()
		{
			if ( $this->authkey == NULL )
				return FALSE;
			
			if ( $userData = $this->db->get_row('SELECT `id`, `last_connexion`, `username`, `email`, `level`, `suspended` FROM `'.USER_TABLE.'` WHERE `authkey` = \''.$this->authkey.'\' LIMIT 1;') )
			{
				$done = TRUE;
				
				/* Account check vars */
				if ( $userData['suspended'] )
				{
					$done = FALSE;
					
					$this->mBus->notification(_MSG_INFO, 'Votre compte a été suspendu !');
				}
				
				/* According account informations */
				if ( $done )
				{
					$this->username = $userData['username'];
					
					$this->id = intval($userData['id']);
					$this->email = $userData['email'];
					$this->level = intval($userData['level']);
					
					/* Update last connexion time */
					if ( intval($userData['suspended']) < (time() - 3600) )
					{
						$this->db->execute('UPDATE `'.USER_TABLE.'` SET `last_connexion` = '.time().' WHERE `id` = '.$this->id.' LIMIT 1;');
					}
				}
				
				return TRUE;
			}
			else
			{
				if ( USER_DEBUG )
					$this->mBus->add(_MSG_ERROR, 'The authkey is not in db.');
				
				/* Unexist authentification key */
				$this->destroy_client_authkey();
				
				return FALSE;
			}
		}
		
		/* Authentification Key generation */
		private function generate_authkey ()
		{
			$newKey = NULL;
			
			/* First part of the authentification key */
			if ( strlen($this->username) < 32 )
			/* username too short, we complete it with random chars */
			{
				$rndPattern = md5( $this->username . rand(0, time()) );
				$charNeeded = 32 - strlen($this->username);
			
				$newKey .= substr($rndPattern, 0, $charNeeded).$this->username;
			}
			elseif ( strlen($this->username) > 32 )
			/* username too long, we cut it */
			{
				$newKey .= substr($this->username, 0, 32);
			}
			else
			/* Same length, just a copy */
			{
				$newKey .= $this->username;
			}
		
			/* Second part of the authentification key */
			$rndPattern = md5( $this->username . rand(0, time()) );
			$newKey .= substr($rndPattern, 0, 32);
			
			if ( strlen($newKey) == 64 )
				return $newKey;
			else
				return NULL;
		}
		
		private function search_authkey ()
		{
			$tmpKey = NULL;
			
			if ( isset($_COOKIE['authkey']) && !empty($_COOKIE['authkey']) )
				$tmpKey = $_COOKIE['authkey'];
			elseif ( isset($_POST['authkey']) && !empty($_POST['authkey']) )
				$tmpKey = $_COOKIE['authkey'];
			elseif ( isset($_GET['authkey']) && !empty($_GET['authkey']) )
				$tmpKey = $_COOKIE['authkey'];
			
			return $tmpKey;
		}
		
		private function save_client_authkey ()
		{
			if ( $this->check_authkey_validity($this->authkey) )
			{
				setcookie('authkey', $this->authkey, time() + USER_AUTHKEY_DAYLIFE * 86400, '/');
				
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		
		private function destroy_client_authkey ()
		{
			setcookie('authkey', NULL, 0, '/');
			
			$this->authkey = NULL;
		}
		
		private function check_authkey_validity ( $authkey )
		{
			if ( !$authkey )
				return FALSE;
			
			if ( !preg_match('#^([a-z0-9-_]{64,64})$#', $authkey) )
				return FALSE;
			
			return TRUE;
		}

		public function checkpoint ($requiredLevel, $goto)
		{
			if ( $this->level < $requiredLevel )
			{
				header('Location:'.$goto);
				echo '<script type="text/javascript">document.location.href = "'.$goto.'";</script>'."\n";
				exit(0);
			}
		}
		
		public function login ($username, $password)
		{
			if ( !preg_match('#^([a-z0-9-_]{4,16})$#', $username) || !preg_match('#^([a-zA-Z0-9-_]{8,32})$#', $password) )
			{
				$this->mBus->notification(_MSG_ERROR, 'Vos informations de connexion sont incorrectes !');
				
				return FALSE;
			}
			
			/* We search after the guy */
			if ( $userData = $this->db->get_row('SELECT `id`, `username`, `password`, `level`, `suspended` FROM `'.USER_TABLE.'` WHERE `username` = \''.$username.'\' LIMIT 1;') )
			{
				if ( md5($password) == $userData['password'] )
				{
					$done = TRUE;
					
					/* Account check vars */
					if ( $userData['suspended'] )
					{
						$done = FALSE;
					
						$this->mBus->notification(_MSG_ERROR, 'Votre compte a été suspendu !');
					}
					
					if ( $done )
					{
						$this->username = $userData['username'];
						
						$this->id = intval($userData['id']);
						$this->level = intval($userData['level']);
						
						/* Create a new key */
						$this->authkey = $this->generate_authkey();
						
						/* Saving key on the server */
						if ( $this->db->execute('UPDATE `'.USER_TABLE.'` SET `authkey` = \''.$this->authkey.'\' WHERE `id` = '.$this->id.' LIMIT 1 ;') > 0 )
						{
							/* Saving key on the client */
							$this->save_client_authkey();
						
							$this->mBus->notification(_MSG_SUCCESS, 'Vous êtes correctement connecté.');
						
							return TRUE;
						}
						else
						{
							$this->mBus->notification(_MSG_ERROR, 'Nous n\'avons pas pu vous connecter !');
						}
					}
				}
				else
				{
					$this->mBus->notification(_MSG_ERROR, 'Le mot de passe ne correspond pas !');
				}
			}
			else
			{
				$this->mBus->notification(_MSG_ERROR, 'Ce compte n\'existe pas !');
			}
			
			return FALSE;
		}
		
		public function logout ()
		{
			if ( $this->authkey != NULL )
			{
				/* we set 'disconnected' as key. This cannot be used,
				because thex connexion method requires a 64 chars length authkey */
				$this->db->execute('UPDATE `'.USER_TABLE.'` SET `authkey` = \'disconnected\' WHERE `id` = '.$this->id.' LIMIT 1 ;');
			
				$this->destroy_client_authkey();
			
				$this->mBus->notification(_MSG_SUCCESS, 'Vous êtes déconnecté !');
			}
		}
		
		public function is_connected ()
		{
			return ( $this->authkey != NULL );
		}
		
		public function get_id ()
		{
			return $this->id;
		}
		
		public function get_username ( $id = 0 )
		{
			if ( !$id )
			{
				return $this->username;
			}
			else
			{
				return $this->db->get_result('SELECT `username` FROM `'.USER_TABLE.'` WHERE `id` = '.intval($id).' LIMIT 1;');
			}
		}
		
		public function get_email ()
		{
			return $this->email;
		}
		
		public function get_level ()
		{
			return $this->level;
		}
		
		function __destruct ()
		{
			/* Nothing to do
			... */
		}
	}
	
	
?>
