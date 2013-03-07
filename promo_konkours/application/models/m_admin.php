<?php 

	class M_Admin extends CI_Model 
	{
		public function checkUser ($data)
		{
			$query = $this->db->get_where('users', $data );
       		return $query->row();
		}

		public function getContestsList ()
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
	        where b.contest_id = '. $contes_id .'order_by status, DESC';

	        $query = $this->db->query($req);
	        return $query->result();
		}

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
			
			redirect('admin/afficher');
		}


	}

?>