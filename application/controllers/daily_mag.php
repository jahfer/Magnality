<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Daily_mag extends CI_Controller {
	
	private $user_id;
	private $allow_access;
	
	function __construct() {
		parent::__construct();	
		$this->is_logged_in();	
		$this->load->model("daily_mag_model");		
		$this->user_id = $this->session->userdata('user_id');
	}
	
	function is_logged_in() {
		$is_logged_in = $this->session->userdata('is_logged_in');
		if(!isset($is_logged_in) || $is_logged_in != true) {
			$this->allow_access = false;	
			$data['uri_redirect'] = $this->uri->uri_string();		
			
			$data['main_content'] = 'access_denied';
			$this->load->view('inc/template', $data);	
		} else 
			$this->allow_access = true;
	}
	
	function ajax_get_discussions($offset = 0) {		
		if(!$this->allow_access) return;
		
		$post_data = $this->daily_mag_model->fetch_discussions($offset);
		
		if($post_data) {
			$data = array('posts' => $post_data, 'base_url'=>base_url());		
			$this->parser->parse("daily_mag_posts_tmpl", $data);
		}
					
	}
	
}