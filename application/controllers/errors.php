<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Errors extends CI_Controller {
	function page_not_found() {		
		$data['main_content'] = 'error_404';
		$this->load->view('inc/template', $data);
	}
}