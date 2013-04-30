<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include(APPPATH.'/controllers/function.php');

/**
* @file admin.php
* @author M. D. (mikanono01@hotmail.com)
* @version 1 (15/03/2013)
* @brief Admin class CI_Controller. 
* @brief Cette class sert à contrôler les affichages, les ajouts, ....
*/

/**
* @class Admin
* @author M. D. (mikanono01@hotmail.com)
* @version 1 (15/03/2013)
* @brief Cette class sert à contrôler les affichages, les ajouts, ....
*/	
class Admin extends CI_Controller 
{
	/** 
	* @brief La function principale.
	* @details Indique lors de la venue sur le site quelle action faire.
	* @details Ici on lui demande de charger les sessions et d'exécuter la fonction afficher.
	*/
	public function index()
	{	
		$this->load->library('session');
		$this->afficher();
	}

	/** 
	* @brief La function afficher.
	* @details Elle récupère tous les concours ainsi que tous les cadeaux.
	* @details Pour se faire on charge le modèle M Admin qui lui comporte la requête Sql adaptée.
	* @details Elle charge ensuite le nom et le titre de la vue à afficher.
	* @details On récupère les paramètres destinés à Javascript s'il y en a pas on initialise le paramètre à 0.
	*/
	public function afficher()
	{
		$this->load->model('M_Admin'); 

		if ( !$this->session->userdata('Connected') ) 
			$this->checkCookie();

		// on récupère les concours.
		$dataList['contests_with_prizes'] = $this->M_Admin->getContestsList();
		// on associe chaque concours à son/ses cadeau(x).
		foreach ( $dataList['contests_with_prizes'] as $key => $value ) 
			$dataList['contests_with_prizes'][$key]['prizes_data'] = $this->M_Admin->getPrizesList($value['id']);

		// Tableau contenant les clés désignant les paramètres utilisés par Javascript.
		$key = array('mb','pb','css','btn','title1','title2','pub','after_title1','after_title2','modal_css','sign','view','form_title','footer','redi');

		// Parcours de l'URL afin de récupère tous les paramètres et des initialise s'il n'existe pas.
		for ( $i = 0; $i < count($key); $i++ )
			$param[$key[$i]] = isset($_GET[$key[$i]]) ? intval($_GET[$key[$i]]) : 0;


		// Affichier l'un ou l'autre affichage
		if ( !$view = isset($_GET['view']) ? $_GET['view'] : 0 )
		{
			$view = 'index';	
		}
		else
		{
			if ( $_GET['view'] == '1' )
				$view = 'index_v2';
			else
				$view = 'index_v3';
		}

		// Récupère le subid
		if ( isset($_GET['subid']) )
		{
			setcookie('subid', $_GET['subid'], time() + 3600, '/');			
		}		 


		$dataList['css']		=  $view;
		$dataList['param']		=  $param;
		$dataLayout['titre']	=  'Promo konkours';
        $dataLayout['vue'] 		=  $this->load->view($view, $dataList ,true);

		$this->load->view('layout', $dataLayout);		
	}
	
	/** 
	* @brief La function checkCookie.
	* @details Elle vérifie si un cookie nommé stats_vistor existe.
	* @details S'il y a un cookie on appelle la fonction pour le lire sinon ont le créer.
	*/
	public function checkCookie ()
	{
		$userExists = false;
		
		if ( isset($_COOKIE['stats_visitor']) && $_COOKIE['stats_visitor'] )
		{
			$data = json_decode($_COOKIE['stats_visitor']);
			
			if ( isset($data->visitor_id) && $data->visitor_id && $this->M_Admin->updateVisitor($data->visitor_id) )
			{
				$userExists = true;
				
				$this->M_Admin->createOrUpdateVisit($data->visitor_id);
			}
		}
		
		if ( !$userExists && ($visitorID = $this->M_Admin->createVisitor()) )
			$this->M_Admin->createOrUpdateVisit($visitorID);
	}

	/** 
	* @brief La function stats.
	* @details Elle permet des récupéré les statistiques des 7 derniers jours.
	* @details On calcule le timestamp d'y a 6 jours et celui d'aujourd'hui.
	* @details On parcourt chaque jour pour récupérer toutes les informations.
	*/
	public function stats () 
	{
		$this->load->model('M_Admin');

		$today = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
		$start = $today - (86400 * 6);
		$end = $start + 86400;

		for ( $i = 0; $i < 7; $i++ )
		{
			$dataList['stats'][$i] = $this->M_Admin->stats($start, $end);
			$dataList['stats'][$i]['date'] = date('d-m-Y ', $start);

			$start += 86400;
			$end += 86400;
		}			
		
		$dataList['type'] = 'stats';
		$dataLayout['vue'] =  $this->load->view('stats', $dataList, true);
		$dataLayout['titre'] = 'Statistiques';
		$this->load->view('layout', $dataLayout);
	}


	/** 
	* @brief La function connect.
	* @details Elle permet de se connecter comme son nom l'indique.
	* @details On charge la librairie Form Validation qui va nous aidera à traiter les informations que l'on va recevoir.
	* @details On crée les règles de validation pour chacun des champs.
	* @details On lance la validation dans un if comme cela si c'est faux on charge l'helper form et on renvoie la page de connexion avec les messages d'erreur.
	* @details Si la vérification est réussie, on crypte le mot de passe, on crée le tableau qui servira de comparaison avec la base de données.
	* @details On charge le model et on appele sa fonction checkUser en lui passant le tableau, si l'utilisateur existe on lui créer une session et on le renvoie à la page d'accueil.
	*/
	public function connect () 
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error">', '</p>');

		$this->form_validation->set_rules('username', 'nom d\'utilisateur ', 'trim|required|min_length[5]|alpha_dash|encode_php_tags|xss_clean');
		$this->form_validation->set_rules('password', 'mot de passe ', 'trim|required|min_length[5]|max_length[64]|alpha_dash|encode_php_tags|xss_clean');

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
		}

		$this->load->helper('form');

		$dataLayout['titre'] 	= 'Connection';
        $dataLayout['vue'] 		=  $this->load->view('login', null ,true);
        $this->load->view('layout', $dataLayout);
	}

	/** 
	* @brief La function Disconnect.
	* @details Elle détruit toutes les sessions et ainsi déconnect l'utilisateur avant de le renvoyer sur la page d'accueil.
	*/
	public function disconnect ()
	{
		$this->session->sess_destroy();
		redirect(base_url());
	}

	/** 
	* @brief La function addContestView.
	* @details Elle sert uniquement à charger l'helper form ainsi que la vue contest qui servira à l'ajout d'un concours.
	*/
	public function addContestView () 
	{
		$this->load->helper('form');

		$dataLayout['titre'] 	=  'Ajoutez un concours';
        $dataLayout['vue'] 		=  $this->load->view('contest', null ,true);
        $this->load->view('layout', $dataLayout);		
	}

	/** 
	* @brief La function addContest.
	* @details Elle sert à l'ajout d'un concours.
	* @details On vérifie que l'on est bien connecté, si on ne l'ait pas on est redirigé vers l'accueil.
	* @details On charge la librairie form validation, on crée également les règles de validations pour les champs.
	* @details Si la validation est correcte, on charge le model M_Admin, on crée le tableau de données et on l'envoie a la fonction addcontest du model.
	* @details On récupère en même temps le dernier id ajouter avant de la passer à la fonction addPrizeVieuw.
	* @details Si la validation a échoué on redirige vers le formulaire d'ajout de concours avec les erreurs et les champs compléter si c'est dernier son valid.
	*/
	public function addContest ()
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$this->load->library('form_validation');			
			$this->form_validation->set_error_delimiters('<p class="error">', '</p>');

			$this->form_validation->set_rules('title', 'titre', 'trim|required|min_length[4]|max_length[255]|encode_php_tags|xss_clean');
			$this->form_validation->set_rules('url', 'lien', 'trim|required|min_length[4]|valid_url');
			$this->form_validation->set_rules('text', 'astuces', 'trim|required|encode_php_tags|xss_clean');
			
			if ( $this->form_validation->run() )
			{
				$this->load->model('M_Admin');
				$data= array('title' => $this->input->post('title'), 
							 'url' => $this->input->post('url'), 
							 'text' => $this->input->post('text')
				);

				// on récupère le dernier id entrer dans la base de données.
				$last_contest_id = $this->M_Admin->addContest($data);
				// on appele la fonction pour ajouter un cadeau et on lui donne l'id.
				$this->addPrizesView($last_contest_id);
			}
			else
			{
				$this->load->helper('form');

				$dataLayout['titre'] 	=  'Ajoutez un concours';
		        $dataLayout['vue'] 		=  $this->load->view('contest', null ,true);
		        $this->load->view('layout', $dataLayout);
			}
	    }
	    else
	    {
	    	redirect(base_url());
	    }
	}

	/** 
	* @brief La function addPrizesView.
	* @param $last_contest_id comporte l'id du concours auquel on va lui ajouter un cadeau.
	* @details Elle sert à charger le helper form ainsi que la vue prize qui servira à l'ajout d'un cadeau, on passe à la vue l'id du concours.
	*/
	public function addPrizesView ( $last_contest_id = 0 )
	{
		// on regarde si l'id est donné par last_contest_id ou par l'URL
		if ( $last_contest_id )
			$contest_id = $last_contest_id;
		else
			$contest_id = $this->uri->segment(3);

		$this->load->helper('form');

		$dataList['id'] 		= $contest_id;
		$dataLayout['titre'] 	=  'Ajoutez un cadeau';
        $dataLayout['vue'] 		=  $this->load->view('prize', $dataList ,true);
        $this->load->view('layout', $dataLayout);
	}

	/** 
	* @brief La function addPrizes.
	* @details Elle sert à l'ajout d'un cadeau.
	* @details On vérifie que l'on est bien connecté, si on ne l'ait pas on est redirigé vers l'accueil.
	* @details On charge la librairie form validation, on crée également les règles de validations pour les champs.
	* @details Si la validation est correcte, on charge le model M_Admin, on crée le tableau de données et on l'envoie a la fonction addprize du model.
	* @details On crée ensuite un tableau avec l'id du concours et celui du cadeau que l'ont envois à la fonction contests_to_prizes.
	* @details On l'id du cadeau ainsi que l'image à la fonction saveImage.
	* @details Si saveimage renvoie une erreur on supprime la dernière entrée de la table prise et on renvoie le formulaire d'ajout de cadeau.
	* @details Si il n'y a pas d'erreur on renvois vers la page d'accueil.
	* @details Si la validation a échoué on redirige vers le formulaire d'ajout de concours avec les erreurs et les champs compléter si c'est dernier son valid.
	*/
	public function addPrize ()
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<p class="error">', '</p>');

			$this->form_validation->set_rules('title', 'titre ', 'trim|required|min_length[4]|max_length[255]|encode_php_tags|xss_clean');
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

				// on récupère le dernier id cadeau rentrer dans la base de données.
				$last_prize_id = $this->M_Admin->addPrize($data);

				$id = array('contest_id' => $contest_id, 'prize_id' => $last_prize_id);

				$this->M_Admin->contests_to_prizes($id);

				// on regarde si l'image renvois une erreur.
				$imageErreur = $this->saveImage($last_prize_id, $image);

				if ( $imageErreur  !== TRUE )
				{
					// on supprime la dernière entrée en cas d'erreur.
					$this->M_Admin->deletePrize($last_prize_id);

					$this->load->helper('form');

					$dataList['erreur']		=  $imageErreur ;
					$dataList['id'] 		=  $contest_id; 
					$dataLayout['titre'] 	=  'Ajoutez un cadeau';
			        $dataLayout['vue'] 		=  $this->load->view('prize', $dataList ,true);
			        $this->load->view('layout', $dataLayout);
				}
				elseif ( $imageErreur == TRUE) 
				{
					redirect(base_url());
				}				
			}
			else
			{
				$this->load->helper('form');

				$dataList['erreur']		=  $erreur;
				$dataList['id'] 		=  $contest_id; 
				$dataLayout['titre'] 	=  'Ajoutez un cadeau';
		        $dataLayout['vue'] 		=  $this->load->view('prize', $dataList ,true);
		        $this->load->view('layout', $dataLayout);
			}			
	    }
	    else
	    {
	    	redirect(base_url());
	    }
	}

	/** 
	* @brief La function addPrizesView.
	* @param $id comporte l'id du cadeau qui servira de nom à l'image.
	* @param $image comporte l'image.
	* @details On teste si on a bien l'id et l'image sinon on return false.
	* @details On crée le nom de l'image et on sélectionne son type grâce au switch.
	* @details On crée une boucle afin de savoir si l'image peut être stockée dans le dossier ou s'il faut en créer un autre pour éviter des problèmes de surcharge.
	* @details On teste si le dossier existe sinon on le crée avant d'écrire dedans.
	* @details On charge la librairie image_lib qui va nous servire a créé une miniature.
	*/
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

			$folderFullSize = getPath('full_size', $id);
			$folderThumbnail = getPath('thumbnail', $id);

			if ( checkRepertory($folderFullSize) == TRUE )
				imagejpeg ($img, $folderFullSize.$nom);
			else
				return $erreur = 'Dossier non inscriptible :' . $folderFullSize;

			if ( checkRepertory($folderThumbnail) === TRUE )
			{
				imagejpeg ($img, $folderThumbnail.$nom);

				// on crée la miniature a partir de l'image enregistrer dans le dossier thumbnail.
				$this->load->library('image_lib');

				$config['image_library'] = 'gd2';
		        $config['source_image'] = $folderThumbnail.$nom;
		        $config['create_thumb'] = FALSE;
		        $config['maintain_ratio'] = TRUE;
		        $config['width'] = 128;
		        $config['height'] = 128;

		        $this->image_lib->initialize($config);
		        $this->image_lib->resize();
			}
			else 
			{
				return $erreur = 'Dossier non inscriptible';
			}

			return TRUE; 
		}	
		return 'Pas d\'image ou d\'id reçu';	
	}

	/** 
	* @brief La function deleteView.
	* @details On récupére le type de contenu que l'on va modifier ainsi que son id grâce à l'URL.
	* @details Suivant le type de contenu que l'on possède on donne une valeur spécifique à dataList['type'].
	* @details On charge la vue en lui donnant les données nécessaires.
	*/
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
		$dataLayout['titre'] 	=  'Supprimer ce '.$type;
        $dataLayout['vue'] 		=  $this->load->view('deleteContent', $dataList ,true);
        $this->load->view('layout', $dataLayout);
	}

	/** 
	* @brief La function delete.
	* @details On vérifie que l'on est bien connecter.
	* @details On récupére le type de contenu que ainsi que son id grâce à l'URL.
	* @details Suivant le type de contenu que l'on possède on exécute une action bien définie.
	* @details Pour un concours on va appeler la fonction qui va l'archiver et pour un cadeau celle qui va le supprimer. 
	*/
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

		        		//  suppresion de l'image a partir de l'id.
						/*
		        		for ( $i = 0; $i < $id; $i++ )
						{
							$n = $i * 100;
							$u = ($i + 1) * 100;

							if ( $n < $id && $id < $u )
							{
								$folderName = $n.'-'.$u.'/';
								break;
							}
						}

		        		if ( file_exists(APPPATH.'../web/uploads/full_size/'.$folderName.$id.'.jpg') )
		        			unlink(APPPATH.'../web/uploads/full_size/'.$folderName.$id.'.jpg'); 

		        		if ( file_exists(APPPATH.'../web/uploads/thumbnail/'.$folderName.$id.'.jpg') )
		        			unlink(APPPATH.'../web/uploads/thumbnail/'.$folderName.$id.'.jpg'); 

		        		*/

		        		$this->M_Admin->deletePrize($id);
		        		redirect('admin/afficher');
		        	break; 
		        }
		    }
		}
		else {
			redirect(base_url());
		}
	}

	/** 
	* @brief La function updateView.
	* @details On récupére le type de contenu que l'on va modifier ainsi que son id grâce à l'URL.
	* @details Suivant le type de contenu que l'on possède on récupère les informations concernant l'id et on déclare la vue que l'on souhaite charger.
	* @details On charge la vue en lui donnant les données nécessaires.
	*/
	public function updateView ()
	{
		$this->load->model('M_Admin');
		$this->load->helper('form');

		$type  = $this->uri->segment(4);
        $id    = $this->uri->segment(3);

        // on récupère les informations d'un cadeau ou d'un concours selon son id
        switch ( $type )
        {
        	case 'contest':
        		$dataList['data'] = $this->M_Admin->getItem($id, 'contests'); 
        		$view = 'contest';
        		break;

        	case 'prize':
        		$dataList['data'] = $this->M_Admin->getItem($id, 'prizes'); 
        		$view = 'prize';
        		break;
        }

        $dataList['id']			=  $id;
        $dataList['type'] 		=  $type;
		$dataLayout['titre'] 	=  'Modifier ce '.$type;
        $dataLayout['vue'] 		=  $this->load->view( $view , $dataList ,true);
        $this->load->view('layout', $dataLayout);
	}


	/** 
	* @brief La function update.
	* @details On vérifie que l'on est bien connecter.
	* @details On charge le model et le helper.
	* @details On récupère le type et l'id.
	* @details Selon le type de contenu on suit des instructions précises.
	* @details On déclare les règles de vérification et on teste les champs pour les deux types, en cas d'erreur on renvois vers le formulaire.
	* @details Pour le type cadeau on vérifie si l'utilisateur a uploadé une nouvelle image, si une image est uploader alors on l'enregistre grace à saveImage.
	* @details Si l'image ne retourne aucune erreur on enregistre les modifications apporter au cadeau.
	* @details Pour le type concours on récupère les informations les vérifient et ont les envois à la fonction update.
	*/
	public function update () 
	{
		if ( $this->session->userdata('Connected') ) 
		{
			$this->load->library('form_validation');
			$this->form_validation->set_error_delimiters('<p class="error">', '</p>');
			$this->load->model('M_Admin');

			$type 	=  $this->input->post('type');
			$id 	=  $this->input->post('id');

			if ( $type == 'prize' )
			{
				$this->form_validation->set_rules('title', 'titre', 'trim|required|min_length[4]|max_length[255]|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('value', 'valeur', 'trim|required|numeric|min_length[1]|max_length[12]|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('position', 'position', 'trim|numeric|encode_php_tags|xss_clean');

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
						$this->M_Admin->update($data, 'prizes', $id); 
					
				}
				else
				{
					$dataList['id']			=  $id;
			        $dataList['type'] 		=  $type;
					$dataLayout['titre'] 	=  'Modifiez ce '.$type;
			        $dataLayout['vue'] 		=  $this->load->view('prize', $dataList ,true);
			        $this->load->view('layout', $dataLayout);
				}
			}
			elseif ( $type == 'contest' )
			{
				$this->form_validation->set_rules('title', ': Titre du concours ', 'trim|required|min_length[5]|max_length[255]|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('url', ': Lien du concours ', 'trim|required|min_length[5]|valid_url');
				$this->form_validation->set_rules('text', ': Astuces pour ce concours ', 'trim|required|encode_php_tags|xss_clean');
				$this->form_validation->set_rules('position', 'position', 'trim|numeric|encode_php_tags|xss_clean');

				if ( $this->form_validation->run() )
				{
					$position = $this->input->post('position');
					$position = isset($position) ? $this->input->post('position') : 0;

					$data= array('title' 	=> $this->input->post('title'), 
								 'url' 		=> $this->input->post('url'), 
								 'text' 	=> $this->input->post('text'),
								 'position' => $position
					);

					$this->M_Admin->update($data, 'contests', $id); 
				}
				else
				{
					$dataList['id']			=  $id;
			        $dataList['type'] 		=  $type;
					$dataLayout['titre'] 	=  'Modifier ce '.$type;
			        $dataLayout['vue'] 		=  $this->load->view('contest', $dataList ,true);
			        $this->load->view('layout', $dataLayout);
				}
			}
		}
		else 
		{
			redirect(base_url());
		}
	}

}
