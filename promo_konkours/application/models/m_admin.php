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
			$this->db->where('id', $data['id']);
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

		/** 
		* @brief La function update.
		* @param $data Comporte toutes les données servant à modification du concours ou du cadeau.
		* @param $table Comporte le nom de la table dans laquelle on doit effectuer la recherche.
		* @param $id Comporte l'id du cadeau ou du concours.
		* @details La fonction met à jour un cadeau ou un concours sur base de son id.
		* @details Cette fonction étant global on doit lui passer en paramètre le nom de la table que l'on souhaite modifier.
		*/
		public function update ($data, $table, $id)
		{
	        if ( $table !== 'visitors' )
	        {
	        	$this->db->where('id', $id); 
	        	$this->db->update($table, $data);
        		redirect('admin/afficher');
        	}
        	else
        	{
        		$req = ' UPDATE visitors SET visit_count = visit_count+1, m_date ='.$data['m_date'].' where id = '.$id;
	        	$this->db->query($req);
        		return true;
        	}
        		
		}

		public function checkVisitor ($data)
		{
			$query = $this->db->get_where('visitors', $data );
       		return $query->row();
		}

		public function setVisitor ($data)
		{
			$this->db->insert('visitors', $data);
			$last_id = $this->db->insert_id();

			return $last_id;
		}

		public function stats ($today, $yesterday) 
		{
			$this->db->where(array('date >=' => $yesterday, 'date <=' => time()));
			$nbClick = $this->db->count_all_results('stats');

			$this->db->where(array('m_date >=' => $yesterday, 'm_date <=' => time()));
			$nbVisit = $this->db->count_all_results('visitors');

			if ( $nbVisit && $nbClick )
				return array('nbClick' => $nbClick, 'nbVisit' => $nbVisit);
			else
				return null;
		}


	}
?>