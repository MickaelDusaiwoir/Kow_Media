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
		$this->load->helper('form');
		/*if ( !$this->session->userdata('Connected') )
		{
			$this->load->helper('form');

			$dataLayout['titre'] 	=  'Administration - Connection';
	        $dataLayout['vue'] 		=  $this->load->view('login', null ,true);
		}
		else 
		{*/
			$this->load->model('M_Admin'); 

			$dataList['contests_with_prizes'] = $this->M_Admin->getContestsList();

			foreach ( $dataList['contests_with_prizes'] as $key => $value ) 
				$dataList['contests_with_prizes'][$key]['prizes_data'] = $this->M_Admin->getPrizesList($value['id']);

			$dataLayout['titre'] 				=  'Administration - Accueil';
	        $dataLayout['vue'] 					=  $this->load->view('index', $dataList ,true);
		//}

		$this->load->view('layout', $dataLayout);		
	}


	public function connect () 
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="alert alert-error">', '</p>');

		$this->form_validation->set_rules('username', ': Nom d\'utilisateur ', 'trim|required|min_length[5]|alpha_dash|encode_php_tags|xss_clean');
		$this->form_validation->set_rules('password', ': Mot de passe ', 'trim|required|min_length[5]|max_length[64]|alpha_dash|encode_php_tags|xss_clean');

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
        $dataLayout['vue'] 		=  $this->load->view('index', null ,true);
        $this->load->view('layout', $dataLayout);
	}

	public function disconnect ()
	{
		$this->session->sess_destroy();
		redirect('admin/afficher');
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
			$this->form_validation->set_error_delimiters('<p class="alert alert-error">', '</p>');

			$this->form_validation->set_rules('title', 'titre', 'trim|required|min_length[5]|max_length[255]|encode_php_tags|xss_clean');
			$this->form_validation->set_rules('url', 'lien', 'trim|required|min_length[5]|prep_url|valid_url|xss_clean');
			$this->form_validation->set_rules('text', 'astuces', 'trim|required|encode_php_tags|xss_clean');
			
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
			else
			{
				$this->load->helper('form');

				$dataLayout['titre'] 	=  'Administration - Ajouter un concours';
		        $dataLayout['vue'] 		=  $this->load->view('addContest', null ,true);
		        $this->load->view('layout', $dataLayout);
			}
	    }
	    else
	    {
	    	redirect('admin/afficher');
	    }
	}

	public function addPrizesView ( $last_contest_id = 0 )
	{
		if ( $last_contest_id )
			$contest_id = $last_contest_id;
		else
			$contest_id = $this->uri->segment(3);

		$this->load->helper('form');

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
			$this->form_validation->set_error_delimiters('<p class="alert alert-error">', '</p>');

			$this->form_validation->set_rules('title', 'titre ', 'trim|required|min_length[5]|max_length[255]|encode_php_tags|xss_clean');
			$this->form_validation->set_rules('value', 'valeur ', 'trim|required|numeric|min_length[1]|max_length[12]|encode_php_tags|xss_clean');
			
			$contest_id = $this->input->post('contest_id');
			
			$image = isset($_FILES['image']) ?  $_FILES['image'] : null ;

			if ( $image['size'] == 0 )
				$erreur = 'Le champ Image est requis.';
			else
				$erreur = null;


			if ( $this->form_validation->run() && $erreur == null )
			{
				$this->load->model('M_Admin');
				$data = array('title' => $this->input->post('title'), 
							 'value' => $this->input->post('value')
				);

				$last_prize_id = $this->M_Admin->addPrize($data);

				//$id = array('contest_id' => $contest_id, 'prize_id' => $last_prize_id);
				$id = array('test_id' => $contest_id, 'test2_id' => $last_prize_id);

				$this->M_Admin->contests_to_prizes($id);

				$imageErreur = $this->saveImage($last_prize_id, $image);

				if ( $imageErreur  !== TRUE )
				{
					$this->M_Admin->deletePrize($last_prize_id);

					$this->load->helper('form');

					$dataList['erreur']		=  $imageErreur ;
					$dataList['id'] 		=  $contest_id; 
					$dataLayout['titre'] 	=  'Administration - Ajouter un cadeau';
			        $dataLayout['vue'] 		=  $this->load->view('addPrize', $dataList ,true);
			        $this->load->view('layout', $dataLayout);
				}
				elseif ( $imageErreur == TRUE) 
				{
					redirect('admin/afficher');
				}				
			}
			else
			{
				$this->load->helper('form');

				$dataList['erreur']		=  $erreur;
				$dataList['id'] 		=  $contest_id; 
				$dataLayout['titre'] 	=  'Administration - Ajouter un cadeau';
		        $dataLayout['vue'] 		=  $this->load->view('addPrize', $dataList ,true);
		        $this->load->view('layout', $dataLayout);
			}			
	    }
	    else
	    {
	    	redirect('admin/afficher');
	    }
	}

	private function saveImage ($id, $image)
	{
		if ( $id && $image)
		{
			$nom = $id.'.jpg';

			switch($image['type']) 
			{			
				case 'image/png':
					$img = imagecreatefrompng($image["tmp_name"]);
				break;
				
				case 'image/jpeg':
					$img = imagecreatefromjpeg($image["tmp_name"]);
				break;

				default: 
					return $erreur = 'L\'image doit être de type png ou jpg';
				break;
			}		

			imagejpeg ($img, 'web/uploads/full_size/'.$nom);
			imagejpeg ($img, 'web/uploads/thumbnail/'.$nom);

			$this->load->library('image_lib');

			$config['image_library'] = 'gd2';
	        $config['source_image'] = 'web/uploads/thumbnail/' . $nom;
	        $config['create_thumb'] = FALSE;
	        $config['maintain_ratio'] = TRUE;
	        $config['width'] = 128;
	        $config['height'] = 128;

	        $this->image_lib->initialize($config);
	        $this->image_lib->resize();

			return TRUE;
		}	
		return $erreur;	
	}

	public function deleteView ()
	{
		$type = $this->uri->segment(4);
        $id = $this->uri->segment(3);

        switch ( $type )
        {
        	case 'contest':
        		$dataList['type'] = 'contest';
        	break;

        	case 'prize':
        		$dataList['type'] = 'prize';
        	break ;
        }

		$dataList['id'] 		=  $id; 
		$dataLayout['titre'] 	=  'Administration - Supprimer ce '.$type;
        $dataLayout['vue'] 		=  $this->load->view('deleteContent', $dataList ,true);
        $this->load->view('layout', $dataLayout);
	}

	public function delete ()
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$type = $this->uri->segment(4);
	        $id = $this->uri->segment(3);

	        $this->load->model('M_Admin');

	        if ( $type && $id )
	        {
		        switch ( $type )
		        {
		        	case 'contest':
		        		$this->M_Admin->archiveContest($id);
		        	break;

		        	case 'prize':
		        		$this->M_Admin->deletePrize($id);
		        		redirect('admin/afficher');
		        	break; 
		        }
		    }
		}

	    redirect('admin/afficher');
	}

	public function updateView ()
	{
		$this->load->model('M_Admin');
		$this->load->helper('form');

		$type  = $this->uri->segment(4);
        $id    = $this->uri->segment(3);

        switch ( $type )
        {
        	case 'contest':
        		$dataList['data'] = $this->M_Admin->getItem($id, 'test'); //contest
        		break;

        	case 'prize':
        		$dataList['data'] = $this->M_Admin->getItem($id, 'test2'); // prizes
        		break;
        }

        $dataList['id']			=  $id;
        $dataList['type'] 		=  $type;
		$dataLayout['titre'] 	=  'Administration - Modifier ce '.$type;
        $dataLayout['vue'] 		=  $this->load->view('update', $dataList ,true);
        $this->load->view('layout', $dataLayout);
	}

	public function update () 
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<p class="alert alert-error">', '</p>');
			$this->load->model('M_Admin');

			$type 	=  $this->input->post('type');
			$id 	=  $this->input->post('id');

			if ( $type == 'prize' )
			{
				$this->form_validation->set_rules('title', ': Titre du cadeau ', 'trim|required|min_length[5]|max_length[255]|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('value', ': Valeur du cadeau ', 'trim|required|numeric|min_length[1]|max_length[12]|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('position', ': Position du cadeau ', 'trim|numeric|encode_php_tags|xss_clean');

				$image = isset($_FILES['image']) ?  $_FILES['image'] : null ;

				if ( $this->form_validation->run() )
				{
					$position = $this->input->post('position');
					$position = isset($position) ? $this->input->post('position') : 0;

					$data = array('title' 	=> $this->input->post('title'), 
								  'value' 	=> $this->input->post('value'),
								  'id'		=> $id,
								  'position'=> $position
					);

					if ( $image !== null )
						$erreur = $this->saveImage($id, $image);

					if ( $erreur == TRUE ) 
						$this->M_Admin->update($data, 'test2', $id); //'prizes'
					
				}
				else
				{
					$dataList['id']			=  $id;
			        $dataList['type'] 		=  $type;
					$dataLayout['titre'] 	=  'Administration - Modifier ce '.$type;
			        $dataLayout['vue'] 		=  $this->load->view('update', $dataList ,true);
			        $this->load->view('layout', $dataLayout);
				}
			}
			elseif ( $type == 'contest' )
			{
				$this->form_validation->set_rules('title', ': Titre du concours ', 'trim|required|min_length[5]|max_length[255]|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('url', ': Lien du concours ', 'trim|required|min_length[5]|prep_url|valid_url|xss_clean');
				$this->form_validation->set_rules('text', ': Astuces pour ce concours ', 'trim|required|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('position', ': Position du concours ', 'trim|numeric|encode_php_tags|xss_clean');

				if ( $this->form_validation->run() )
				{
					$position = $this->input->post('position');
					$position = isset($position) ? $this->input->post('position') : 0;

					$data= array('title' 	=> $this->input->post('title'), 
								 'url' 		=> $this->input->post('url'), 
								 'text' 	=> $this->input->post('text'),
								 'position' => $position
					);

					$this->M_Admin->update($data, 'test', $id); //'contests'
				}
				else
				{
					$dataList['id']			=  $id;
			        $dataList['type'] 		=  $type;
					$dataLayout['titre'] 	=  'Administration - Modifier ce '.$type;
			        $dataLayout['vue'] 		=  $this->load->view('update', $dataList ,true);
			        $this->load->view('layout', $dataLayout);
				}
			}
		}

		redirect('admin/afficher');
	}


}

