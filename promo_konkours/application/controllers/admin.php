<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	
	public function index()
	{
		$this->afficher();
	}

	public function afficher()
	{
		$this->load->model('M_Admin');
		$tst = $this->checkDb();
		echo $tst; 
	}
}

