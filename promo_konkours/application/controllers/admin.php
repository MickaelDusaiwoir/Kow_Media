<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

// $this->load->model('M_Admin');
// $this->load->library('form_validation');
// $this->load->helper('form');
	
	public function index()
	{	
		$this->load->library('session');
		$this->afficher();
	}

	public function afficher()
	{	
		if ( !$this->session->userdata('Connected') )
		{
			$this->load->helper('form');

			$dataLayout['titre'] 	=  'Administration - Connection';
	        $dataLayout['vue'] 		=  $this->load->view('login', null ,true);
		}
		else 
		{
			$this->load->model('M_Admin'); 

			$contests = $this->M_Admin->getContestsList();

			foreach ( $contests as $contest ) 
			{
				$prize = $this->M_Admin->getPrizesList($contest->id);
				$dataList['contests'] .= $contest.$prize; 
			}		

			//$dataList['contests'] 				=  $contests;
			//$dataList['contests']['prizes'] 	=  $;
			$dataLayout['titre'] 				=  'Administration - Accueil';
	        $dataLayout['vue'] 					=  $this->load->view('index', $dataList ,true);
		}

		$this->load->view('layout', $dataLayout);		
	}


	public function connect () 
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('username', '"Nom d\'utilisateur"', 'trim|required|min_length[5]|alpha_dash|encode_php_tags|xss_clean');
		$this->form_validation->set_rules('password', '"Mot de passe"', 'trim|required|min_length[5]|max_length[64]|alpha_dash|encode_php_tags|xss_clean');

		if ( $this->form_validation->run() )
		{
			$pwd 	=  $this->input->post('password');
			$pwd 	=  md5($pwd);
			$data 	=  array( 'username' => $this->input->post('username'), 'password' => $pwd );

			$this->load->model('M_Admin');

			if ( $this->M_Admin->checkUser($data) )
			{
				$this->session->set_userdata('Connected', true);
				redirect('admin/afficher');
			}
			else 
			{
				echo "Utilisateur et/ou mot de passe inconnu";
			}
		}

		$this->load->helper('form');

		$dataLayout['titre'] 	= 'Administration - Connection';
        $dataLayout['vue'] 		=  $this->load->view('login', null ,true);
        $this->load->view('layout', $dataLayout);
	}

	public function addContest () 
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$this->load->helper('form');

			$dataLayout['titre'] 	=  'Administration - Ajouter un concours';
	        $dataLayout['vue'] 		=  $this->load->view('addContest', null ,true);
	        $this->load->view('layout', $dataLayout);
		}
		else
		{
			redirect('admin/login');
		}
	}


}

