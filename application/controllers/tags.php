<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Tags
 * 
 * Handles searching the database for specific tags
 *
 * @author Jahfer Husain
 */

class Tags extends CI_Controller {

	private $user_id;
	
	function __construct() {
		parent::__construct();
		$this->is_logged_in();
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
	
	function search_tags($tag, $is_ajax = FALSE, $offset = 0) {
		if(!$this->allow_access) return;
		
		$this->load->model('tag_model');
		$data['stream_data'] = $this->tag_model->run_tag_search(url_title($tag), $offset);	
		
		$this->load->model('post_model');
			
		$data['search_string'] = str_replace("_", " ", $tag);
		$this->load->helper('text');
		$data['tag_url'] = url_title($tag);
		
		if($is_ajax)
			echo $this->post_model->print_stream($data['stream_data']);
		else {
			$this->load->helper('text');			
			$this->load->model("sidebar_model");
			$this->load->model("filter_model");
			$data['main_content'] = 'stream_ui';
			$this->load->view('inc/template', $data);	
		}	
	}
	
}