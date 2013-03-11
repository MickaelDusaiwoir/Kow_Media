<?php 

	class M_Admin extends CI_Model 
	{
		public function checkUser ($data)
		{
			$query = $this->db->get_where('users', $data );
       		return $query->row();
		}

	/*	public function getContestsList ()
		{
			$this->db->order_by("position", "desc");
        	$query = $this->db->get_where('contests', array('status' => '0' ));
        	return $query->result();
		}

		public function getPrizesList ($contest_id)
		{
			$req = ' select title, value, id
			from prizes as a 
	        inner join contests_to_prizes as b on a.id = b.prizes_id
	        where b.contest_id = '. $contes_id .'order_by position, DESC';

	        $query = $this->db->query($req);
	        return $query->result();
		}	

		public function addContest ($data)
		{
			$this->db->insert('contests', $data);
			$last_contest_id = $this->db->insert_id();

			return $last_contest_id;
		}

		public function addPrize ($data)
		{
			$this->db->insert('prize', $data);
			$last_prize_id = $this->db->insert_id();

			return $last_prize_id;
		}

		public function contests_to_prizes ($data)
		{
			$this->db->insert('contests_to_prizes', $data);
		}

		public function archiveContest ($id) 
		{
			$this->db->where('id', $data['id']);
			$this->db->update('contest', array('status' => '1'));
			
			redirect('admin/afficher');
		}

		public function deletePrize ($id)
		{
			$req = ' delete *
			from prizes as a 
	        inner join contests_to_prizes as b on a.id = b.prize_id
	        where b.prize_id = '. $id;

	        $this->db->query($req);

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
        	$query = $this->db->get('test');
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