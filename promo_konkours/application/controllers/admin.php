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

		$this->form_validation->set_rules('username', '" Nom d\'utilisateur "', 'trim|required|min_length[5]|alpha_dash|encode_php_tags|xss_clean');
		$this->form_validation->set_rules('password', '" Mot de passe "', 'trim|required|min_length[5]|max_length[64]|alpha_dash|encode_php_tags|xss_clean');

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

	public function disconnect ()
	{
		$this->session->sess_destroy();
		redirect('admin/connect');
	}

	public function addContestView () 
	{
		$this->load->helper('form');

		$dataLayout['titre'] 	=  'Administration - Ajouter un concours';
        $dataLayout['vue'] 		=  $this->load->view('addContest', null ,true);
        $this->load->view('layout', $dataLayout);		
	}

	public function addContest ()
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('title', '" Titre du concours "', 'trim|required|min_length[5]|max_length[255]|encode_php_tags|xss_clean');
			$this->form_validation->set_rules('url', '" Lien du concours "', 'trim|required|min_length[5]|prep_url|valid_url|xss_clean');
			$this->form_validation->set_rules('text', '" Astuces pour ce concours "', 'trim|required|encode_php_tags|xss_clean');
			
			if ( $this->form_validation->run() )
			{
				$this->load->model('M_Admin');
				$data= array('title' => $this->input->post('title'), 
							 'url' => $this->input->post('url'), 
							 'text' => $this->input->post('text')
				);

				$last_contest_id = $this->M_Admin->addContest($data);
				$this->addPrizesView($last_contest_id);
			}

			$this->load->helper('form');

			$dataLayout['titre'] 	=  'Administration - Ajouter un concours';
	        $dataLayout['vue'] 		=  $this->load->view('addContest', null ,true);
	        $this->load->view('layout', $dataLayout);
	    }
	    else
	    {
	    	redirect('admin/connect');
	    }
	}

	public function addPrizesView ( $last_contest_id = 0 )
	{
		if ( $last_contest_id )
			$contest_id = $last_contest_id;
		else
			$contest_id = $this->uri->segment(3);

		$dataList['id'] 		= $contest_id;
		$dataLayout['titre'] 	=  'Administration - Ajouter un concours';
        $dataLayout['vue'] 		=  $this->load->view('addPrize', $dataList ,true);
        $this->load->view('layout', $dataLayout);
	}

	public function addPrize ()
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('title', '" Titre du cadeau "', 'trim|required|min_length[5]|max_length[255]|encode_php_tags|xss_clean');
			$this->form_validation->set_rules('img', '" Image du cadeau "', 'required');
			$this->form_validation->set_rules('value', '" Valeur du cadeau "', 'trim|required|numeric|encode_php_tags|xss_clean');
			
			$contest_id = $this->input->post('contest_id');

			if ( $this->form_validation->run() )
			{
				$image = $this->input->post('img');

				$this->load->model('M_Admin');
				$data = array('title' => $this->input->post('title'), 
							 'value' => $this->input->post('value')
				);

				$last_prize_id = $this->M_Admin->addPrize($data);

				//$id = array('contest_id' => $contest_id, 'prize_id' => $last_prize_id);
				$id = array('test_id' => $contest_id, 'test2_id' => $last_prize_id);

				$this->M_Admin->contests_to_prizes($id);

				/////////////////////////////// image //////////////////////////
				$this->saveImage($last_prize_id, $image);
			}

			$this->load->helper('form');

			$dataList['id'] 		=  $contest_id; 
			$dataLayout['titre'] 	=  'Administration - Ajouter un concours';
	        $dataLayout['vue'] 		=  $this->load->view('addPrize', $dataList ,true);
	        $this->load->view('layout', $dataLayout);
	    }
	    else
	    {
	    	redirect('admin/connect');
	    }
	}

	private function saveImage ($id, $src)
	{
		if ( $id && $src)
		{
			$img = file_get_contents($src);
			$image = imagecreatefromjpeg($src);
			$nom = $id.'jpg';

			imagejpeg ($image, 'web/uploads/full_size/'.$nom);
			imagejpeg ($image, 'web/uploads/thumbnail/'.$nom);

	        //file_put_contents('web/uploads/' . $nom, $image);

			// thumbnail

			$config['image_library'] 	= 'gd2';
	        $config['source_image'] 	= 'web/uploads/thumbnail/' . $nom;
	        $config['create_thumb'] 	= false;
	        $config['maintain_ratio'] 	= TRUE;
	        $config['width'] 			= 128;
	        $config['height'] 			= 128;

			$this->load->library('image_lib', $config);
		}		
	}

}

