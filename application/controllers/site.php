<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Site 
 * 
 * Controller for most pages throughout site
 *
 * @author Jahfer Husain
 */

class Site extends CI_Controller {
	
	private $allow_access;
	private $user_id;
	
	function __construct() {			
		parent::__construct();
		$this->is_logged_in();
		$this->user_id = $this->session->userdata('user_id');
		$this->output->enable_profiler();
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
	
	function home() {
		if(!$this->allow_access) return;		
		$this->load->model('daily_mag_model');
		$this->load->model('filter_model');
		$this->load->model('sidebar_model');
		
		// fetch discussions
		$posts = $this->daily_mag_model->fetch_discussions();	
			
		if($posts)	{				
			
			// template data
			$data = array(
				'posts' 				=> $posts,
				'tag_list' 			=> $this->daily_mag_model->fetch_tag_concat(),
				'pics'				=> $this->daily_mag_model->fetch_pics(),
				'notifications' 	=> $this->daily_mag_model->fetch_notifications(),
				'links' 				=> $this->daily_mag_model->fetch_links(),
				'docs' 				=> $this->daily_mag_model->fetch_docs(),
				'base_url' 			=> base_url()
			);				
			
			$this->parser->parse('daily_mag_template', $data);		
			
		} else {			
			$data = array('notifications' => $this->daily_mag_model->fetch_notifications());		
			$this->parser->parse('no_messages', $data);
		}
	}
	
	function profile_settings() {
		if(!$this->allow_access) return;
		
		$data['display_picture_url'] = $this->membership_model->fetch_dp($this->user_id);
		
		$this->load->model('profile_model');
		$data['profile'] = $this->profile_model->load_data($this->user_id);
		
		
		$data['status'] = $this->session->flashdata('profile');
		
		switch($data['status']) {
			case 1:
				$data['status_msg'] = "Successfully updated profile.";
				$data['class'] = "class=success";
				break;
			case 2:
				$data['status_msg'] = "Profile update failed.";
				$data['class'] = "class=error";
				break;
			default:
				$data['status_msg'] = "";
				$data['class'] = "";
		}
		
		$data['main_content'] = 'profile_settings';
		$this->load->view('inc/template', $data);	
	}
	
	function profile($user_id) {
		if(!$this->allow_access) return;
		
		$this->load->model('profile_model');
		$data['profile'] = $this->profile_model->load_data($user_id);
		
		if( count($data['profile']) == 0 )
			show_404('profile');			
		
		$this->load->model("filter_model");
		$data['filters'] = $this->filter_model->fetch_user_filters($user_id);
		$data['user_id'] = $user_id;
		
		$this->load->model('post_model');
		$data['stream_data'] = $this->profile_model->load_recent_posts($user_id);
		
		$data['main_content'] = 'profile';
		$this->load->view('inc/template', $data);	
	}
	
	function pics() {		
		if(!$this->allow_access) return;
		
		$this->load->model("media_model");
		$data['pics'] = $this->media_model->fetch_pics();
		
		$data['main_content'] = 'pics';
		$this->load->view('inc/template', $data);
		
		$this->output->cache(5);
	}
	
	function docs() {		
		if(!$this->allow_access) return;
		
		$this->load->model("media_model");
		$data['docs'] = $this->media_model->fetch_docs();
		
		$data['main_content'] = 'docs';
		$this->load->view('inc/template', $data);
		
		$this->output->cache(5);
	}
	
}