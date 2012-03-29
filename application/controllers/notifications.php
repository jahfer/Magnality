<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Notifications
 * 
 * Handle all notification routing for marking as read
 * and loading all notifications
 *
 * @author Jahfer Husain
 */

class Notifications extends CI_Controller {
	
	private $allow_access;
	private $user_id;
	
	function __construct() {			
		parent::__construct();
		$this->is_logged_in();
		$this->user_id = $this->session->userdata('user_id');
		$this->load->model("notification_model");
		//$this->output->enable_profiler();
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
	
	function index() {		
		if(!$this->allow_access) return;
		
		$data['query'] = $this->notification_model->fetch_notifications();
		
		$data['main_content'] = 'notifications';
		$this->load->view('inc/template', $data);
	}
	
	function mark_read() {
		if(!$this->allow_access) return;
		$this->notification_model->mark_as_read($this->user_id);
		
	}
	
}