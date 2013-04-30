<?php 

	/**
	* @file m_admin.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (15/03/2013)
	* @brief M_Admin class CI_Model. 
	* @brief Cette Class sert à faire toutes les requêtes pour l'affichage, l'ajout, ...
	*/

	/**
	* @class M_Admin
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (15/03/2013)
	* @brief Cette Class sert à faire toutes les requêtes pour l'affichage, l'ajout, ...
	*/	
	class M_Admin extends CI_Model 
	{
		/** 
		* @brief La function checkUser.
		* @param $data comporte les informations de l'utilisateur (mot de passe, nom d'utilisateur).
		* @details la fonction renvois une ligne si elle trouve une correspondance.
		*/
		public function checkUser ($data)
		{
			$query = $this->db->get_where('users', $data );
       		return $query->row();
		}	

		/** 
		* @brief La function getContestsList.
		* @details la fonction renvoie tous les concours dans l'ordre descendent de leur position et qui ont un status égale à 0.
		* @details status = 0 => concours visible, status = 1 => concours archiver
		*/
		public function getContestsList ()
		{
			$this->db->order_by("position", "desc");
        	$query = $this->db->get_where('contests', array('status' => '0' ));
        	return $query->result_array();
		}
		
		/** 
		* @brief La function getPrizesList.
		* @param $contest_id contient l'id du concours.
		* @details la fonction renvoie tous les cadeaux correspondant à ce concours.
		*/
		public function getPrizesList ($contest_id)
		{
			$req = ' select title, value, id
			from prizes as a 
	        inner join contests_to_prizes as b on a.id = b.prize_id
	        where b.contest_id = '. $contest_id .' ORDER BY a.position DESC';

	        $query = $this->db->query($req);
	        return $query->result_array();
		}	
		
		/** 
		* @brief La function addContest.
		* @param $data comporte les informations du concours (titre, url, astuce).
		* @details la fonction renvois une ligne si elle trouve une correspondance.
		*/
		public function addContest ($data)
		{
			$this->db->insert('contests', $data);
			$last_contest_id = $this->db->insert_id();

			return $last_contest_id;
		}
	
		/** 
		* @brief La function addPrize.
		* @param $data comporte les informations du concours (titre, value).
		* @details la fonction renvoie le dernier id rentré dans la base de données.
		*/
		public function addPrize ($data)
		{
			$this->db->insert('prizes', $data);
			$last_prize_id = $this->db->insert_id();

			return $last_prize_id;
		}

		/** 
		* @brief La function contests_to_prizes.
		* @param $data comporte l'id du concours et du cadeau.
		* @details la fonction les ajoute dans la table de liaison.
		*/
		public function contests_to_prizes ($data)
		{
			$this->db->insert('contests_to_prizes', $data);
		}

		/** 
		* @brief La function archiveContest.
		* @param $id comporte l'id du concours.
		* @details la fonction change le status du concours.
		*/
		public function archiveContest ($id) 
		{
			$this->db->where('id', $id);
			$this->db->update('contests', array('status' => '1'));
			
			redirect('admin/afficher');
		}

		/** 
		* @brief La function deletePrize.
		* @param $id comporte l'id du cadeau.
		* @details la fonction supprime le cadeau de la table prise mais également de la table de liaison.
		*/
		public function deletePrize ($id)
		{
			$req = ' delete from prizes where id = '. $id;
	        $this->db->query($req);

	        $req = ' delete from contests_to_prizes where prize_id = '. $id;
	        $this->db->query($req);

	        return true;
		}

		/** 
		* @brief La function getItem.
		* @param $id comporte l'id du cadeau ou du concours.
		* @param $table comporte le nom de la table dans laquelle on doit effectuer la recherche.
		* @details la fonction supprime le cadeau de la table prise mais également de la table de liaison.
		*/
		public function getItem($id, $table)
		{
			$query = $this->db->get_where($table, array('id' => $id));
			return $query->row();
		}
		
		function createCookie ($visitorID, $date)
		{
			if ( $visitorID && $date )
			{
				$data = array(
					'visitor_id' => $visitorID,
					'm_date' => $date
				);
				
				setcookie('stats_visitor', json_encode($data), time() + (365*86400), '/');
				
				return true;
			}
			else
			{
				return false;
			}
		}
		
		function destroyCookie ()
		{
			setcookie('stats_visitor', null, 0, '/');
		}
		
		public function createVisitor ()
		{
			$data = array(
				'ip' => ip2long($_SERVER['REMOTE_ADDR']), 
				'c_date' => time(), 
				'm_date' => 0, 
				'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null, 
				'source' => isset($_GET['src']) ? $_GET['src'] : null, 
				'visit_count' => '1'
			); 
			
			if ( $this->db->insert('visitors', $data) )
			{
				$lastID = $this->db->insert_id();
				
				if ( $lastID && $this->createCookie($lastID, $data['c_date']) )
					return $lastID;
			}
			
			return false;
		}
		
		public function updateVisitor ($visitor_id)
		{
			$time = time();
			
			$query = 
				'UPDATE `visitors` SET '.
				'`m_date` = '.$time.', '.
				'`visit_count` = `visit_count` + 1 '.
				'WHERE `id` = '.$visitor_id.' LIMIT 1;';
			
			if ( ($result = $this->db->query($query)) !== false )
			{
				if ( $this->db->affected_rows() > 0 )
					return $this->createCookie($visitor_id, $time);
				else
					$this->destroyCookie();
			}
			
			return false;
		}
		
		public function createOrUpdateVisit ($visitor_id)
		{
			$date = date('d-m-Y');
			
			$query = 
				'INSERT INTO `visits` (`visitor_id`, `visit_count`, `date`) '.
				'VALUES ('.$visitor_id.', 1, \''.$date.'\') '.
				'ON DUPLICATE KEY UPDATE `visit_count` = `visit_count` + 1;';
			
			$result = $this->db->query($query);
			
			return ( $result && $this->db->affected_rows() > 0 );
		}
		
		/** 
		* @brief La function update.
		* @param $data Comporte toutes les données servant à modification du concours ou du cadeau ainsi que des statistiques.
		* @param $table Comporte le nom de la table dans laquelle on doit effectuer la recherche.
		* @param $id Comporte l'id du cadeau ou du concours ainsi que celui des statistiques.
		* @details La fonction met à jour un cadeau ou un concours sur base de son id.
		* @details Cette fonction étant global on doit lui passer en paramètre le nom de la table que l'on souhaite modifier.
		*/
		public function update ($data, $table, $id)
		{
        	$this->db->where('id', $id); 
        	$this->db->update($table, $data);
        	
    		redirect('admin/afficher');
		}

		/** 
		* @brief La function stats.
		* @param $start comporte la date d'un jour en timestamp.
		* @param $end comporte la date du lendemain de $start en timestamp.
		* @details retourne le nombre de clics; le nombre de vue, le nombre de clics uniques ainsi que la moyen des concours vus.
		*/
		public function stats ($start, $end) 
		{
			// Nombre de clics par jour.
			$req = 'SELECT count(id) FROM stat_contest_click WHERE `date`>="'.$start.'" AND `date`<="'.$end.'";';
			$query = $this->db->query($req);
			$nbClick = $query->result_array();

			// Nombre de vues par jour.
			$req = 'SELECT SUM(visit_count) FROM visits WHERE `date`="'. date('d-m-Y', $start).'";';
			$query = $this->db->query($req);
			$nbVisit = $query->result_array();

			// nombre de clics uniques.
			$req = 'SELECT count(DISTINCT visitor_id) FROM stat_contest_click WHERE `date`>='.$start.' AND `date`<='.$end;
			$query = $this->db->query($req);
			$clickUnique = $query->result_array();

			// Moyenne des concours vus par les utilisateurs.
			$req = 'SELECT AVG(click_count) FROM stats_visitors_click WHERE `date` = "'.date('d-m-Y', $start).'";';
			$query = $this->db->query($req);
			$avgClicK = $query->result_array();

			return array(
							'nbClick' => $nbClick[0]['count(id)'], 
							'nbVisit' => $nbVisit[0]["SUM(visit_count)"], 
							'clickUnique' => $clickUnique[0]['count(DISTINCT visitor_id)'],
							'avgClick' => $avgClicK[0]['AVG(click_count)']
			); 
		}


	}
?>
