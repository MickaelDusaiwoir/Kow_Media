<?php 

	/**
	* @file m_admin.php
	* @author M. D. (mikanono01@hotmail.com)
	* @version 1 (15/03/2013)
	* @brief M_Admin class CI_Model. 
	* @brief Cette Class sert à faire toutes les requêtes pour l'affichage, l'ajout, ...
	*/

	/**
	* @class Admin
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
		/*public function getContestsList ()
		{
			$this->db->order_by("position", "desc");
        	$query = $this->db->get_where('contests', array('status' => '0' ));
        	return $query->result();
		}
		
		/** 
		* @brief La function getPrizesList.
		* @param $contest_id contient l'id du concours.
		* @details la fonction renvoie tous les cadeaux correspondant à ce concours.
		*/
		/*public function getPrizesList ($contest_id)
		{
			$req = ' select title, value, id
			from prizes as a 
	        inner join contests_to_prizes as b on a.id = b.prizes_id
	        where b.contest_id = '. $contes_id .'order_by position, DESC';

	        $query = $this->db->query($req);
	        return $query->result();
		}	
		
		/** 
		* @brief La function addContest.
		* @param $data comporte les informations du concours (titre, url, astuce).
		* @details la fonction renvois une ligne si elle trouve une correspondance.
		*/
		/*public function addContest ($data)
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
		/*public function addPrize ($data)
		{
			$this->db->insert('prize', $data);
			$last_prize_id = $this->db->insert_id();

			return $last_prize_id;
		}

		/** 
		* @brief La function contests_to_prizes.
		* @param $data comporte l'id du concours et du cadeau.
		* @details la fonction les ajoute dans la table de liaison.
		*/
		/*public function contests_to_prizes ($data)
		{
			$this->db->insert('contests_to_prizes', $data);
		}

		/** 
		* @brief La function archiveContest.
		* @param $data comporte l'id du concours.
		* @details la fonction change le status du concours.
		*/
		/*public function archiveContest ($id) 
		{
			$this->db->where('id', $data['id']);
			$this->db->update('contest', array('status' => '1'));
			
			redirect('admin/afficher');
		}

		/** 
		* @brief La function deletePrize.
		* @param $data comporte l'id du cadeau.
		* @details la fonction supprime le cadeau de la table prise mais également de la table de liaison.
		*/
		/*
		public function deletePrize ($id)
		{
			$req = ' delete *
			from prizes as a 
	        inner join contests_to_prizes as b on a.id = b.prize_id
	        where b.prize_id = '. $id;

	        $this->db->query($req);

	        redirect('admin/afficher');
		}

		/** 
		* @brief La function getItem.
		* @param $id comporte l'id du cadeau ou du concours.
		* @param $table comporte le nom de la table dans laquelle on doit effectuer la recherche.
		* @details la fonction supprime le cadeau de la table prise mais également de la table de liaison.
		*/
		/*
		public function getItem($id, $table)
		{
			$query = $this->db->get_where($table, array('id' => $id));
			return $query->row();
		}

		public function update ($data, $table, $id)
		{
			$this->db->where('id', $id); 
	        $this->db->update($table, $data);

        	redirect('admin/afficher');
		}


		*/

		///////////////////////////////////////////////////////////////  table de teste 

		public function addContest ($data)
		{
			$this->db->insert('test', $data);
			$last_contest_id = $this->db->insert_id();

			return $last_contest_id;
		}


		public function addPrize ($data)
		{
			$this->db->insert('test2', $data);
			$last_prize_id = $this->db->insert_id();

			return $last_prize_id;
		}

		public function contests_to_prizes ($data)
		{
			$this->db->insert('test_to_test2', $data);
		}

		public function getContestsList ()
		{
        	$query = $this->db->get_where('test', array('status' => '0'));
        	return $query->result_array();
		}

		public function getPrizesList ($contest_id)
		{
			$req = ' select title, value, id
			from test2 as a 
	        inner join test_to_test2 as b on a.id = b.test2_id
	        where b.test_id = '. $contest_id;

	        $query = $this->db->query($req);
	        return $query->result_array();
		}

		public function deletePrize ($id)
		{
			$req = ' delete from test2 where id = '. $id;
	        $this->db->query($req);

	        $req = ' delete from test_to_test2 where test2_id = '. $id;
	        $this->db->query($req);

	        return true;
		}

		public function archiveContest ($id) 
		{
			$this->db->where('id', $id);
			$this->db->update('test', array('status' => '1'));
			
			redirect('admin/afficher');
		}


		public function getItem($id, $table)
		{
			$query = $this->db->get_where($table, array('id' => $id));
			return $query->row();
		}

		public function update ($data, $table, $id)
		{
			$this->db->where('id', $id); 
	        $this->db->update($table, $data);

        	redirect('admin/afficher');
		}

	}

?>