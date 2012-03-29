<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class About extends CI_Controller {
	
	function my_mag() {
		
		$data = array(
			'image' => base_url()."img/MyMagTour.png"	
		);
		
		$this->load->view('my_mag_tour', $data);
		
		$this->output->cache(3600);
	
	}
	
}