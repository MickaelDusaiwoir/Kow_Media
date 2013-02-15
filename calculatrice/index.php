<?php
	
	header('content-type: text/html; charset=utf-8');
	
	class calculatrice {
	
		private $operations = 0;
		private $result = 0;
		
		public function __construct ($operations = 0) {
		
			if ( $operations != 0 )
				$this->ajoutNombre($int);
		
		}
		
		public function ajoutNombre ($int) {
			
			if(!is_nan($int))
				$this->operations = $int;
			else
				echo 'Ce n\'est pas un nombre';
		
		}
		
		public function plus ($int) {
			$this->operations += $int ; 
		}
		
		public function moins ($int) {
			$this->operations -= $int ; 
		}

		public function multiplie ($int) {
			$this->operations *= $int ; 
		}
		
		public function divise ($int) {
			$this->operations /= $int ; 
		}
		
		public function clear() {
			if($this->operations)
				$this->operations = 0;
			if($this->result)
				$this->result = 0;
		}
		
		public function result() {
			if($this->operations) {
				$this->result = $this->operations;
				echo $this->result;
				$this->clear();
			} else {
				echo 'Aucune opération en cours';
			}
		}
		
	}
	
	$calcul = new calculatrice();
	$calcul->ajoutNombre(4);
	$calcul->plus(8);
	$calcul->result();

?>