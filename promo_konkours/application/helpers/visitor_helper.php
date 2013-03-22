<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class visitor 
	{
		function checkCookie ()
		{
			if ( !isset($_COOKIE['data']) )
			{
				$ip = $_SERVER['REMOTE_ADDR'];
				$referer = isset($_SERVER['HTTP_REFERER']);
				$source = isset($_GET['src']);
				$date = time();


				$data = array('ip' => $ip, 'c_date' => $date, 'm_date' => $date, 'referer' => $referer, 'source' => $source, 'visit_count' => '1' ); 

				$this->load->model('M_Visitor');


			}
			else
			{

			}
		}

	}

?>