<?php 

	class M_Admin extends CI_Model 
	{

		public function checkDb() 
		{
			$query = $this->db->get('promo_konkours');
        	return $query->result();
		}




	}

?>