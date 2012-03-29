<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Posts
 * 
 * Handles all situations regarding posts, including
 * adding, deleting posts and comments; also tags and search.
 *
 * This handles the editing of tags as it is strictly related
 * to a single post for the editing. The Tags class is used for
 * searching based on tags.
 *
 * @author Jahfer Husain
 */

class Posts extends CI_Controller {

	private $user_id;
	private $allow_access;
	
	function __construct() {
		parent::__construct();	
		$this->is_logged_in();	
		$this->load->model("post_model");		
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

	function add() {
		if(!$this->allow_access) return;
		
		$this->post_model->add_post();
		redirect('home');
	}
	
	function remove() {
		if(!$this->allow_access) return;
		
		if($this->input->post('id')) {
			$this->post_model->remove_post($this->user_id);
		}
	}
	
	function edit_tags() {
		if(!$this->allow_access) return;
		
		$post_id = $this->input->post('id');
		$new_tags = $this->input->post('new_tags');
		
		$this->post_model->update_post_tags($post_id, $new_tags);		
		
		$this->load->model("notification_model");
		$this->notification_model->add_notification($this->user_id, $post_id, "tag");
		
		echo $this->post_model->parse_tags($new_tags);
	}
	
	function permalink($post_id) {
		if(!$this->allow_access) return;
	
		$data['post'] = $this->post_model->fetch_post_data($post_id);
		$data['history'] = $this->post_model->fetch_tag_history($post_id);			
		
		if($data['post']) {
			$data['comments'] = $this->post_model->fetch_comments($post_id);	
			$data['main_content'] = 'permalink';
		} else
			show_404('posts');			
		
		$this->load->view('inc/template', $data);	
	}
	
	function add_comment() {
		if(!$this->allow_access) return;
		
		if($this->input->post('submit')) {
			$this->post_model->add_user_comment($this->user_id);
		}
		
		redirect($this->input->post('uri_redirect'));
	}
	
	function remove_comment($id, $post_id) {		
		if(!$this->allow_access) return;		
		$this->post_model->remove_comment($id, $this->user_id);
		
		redirect("posts/$post_id");
	}
	
	function add_vote() {
		if(!$this->allow_access) return;
		$this->post_model->add_vote($this->user_id);
	}
	
	
// AJAX STREAM LOAD	
	function load($sort="magic", $offset = 0) {
		$stream_data = $this->post_model->fetch_stream($sort, $offset);
		echo $this->post_model->print_stream($stream_data);
	}
// END OF AJAX LOAD
	
	
	
// SEARCH
	function search_redirect() {
		$str = str_replace(" ", "+", $this->input->post('site-search'));
		$str = ($str) ? $str : "null";
		redirect('search/'.$str); 
	}
	
	function search($str, $is_ajax = FALSE, $offset = 0) {	
		if(!$this->allow_access) return;
	
		$data['stream_data'] = $this->post_model->run_search($str, $offset);		
		$data['search_string'] = str_replace("+", " ", $str);		
		$data['orig_str'] = $str;
		$data['search_url'] = url_title($str);
		
		if($is_ajax)
			echo $this->post_model->print_stream($data['stream_data']);
		else {
			$this->load->model("sidebar_model");
			$this->load->model("filter_model");
			$this->load->helper('text');
			$data['main_content'] = 'stream_ui';
			$this->load->view('inc/template', $data);	
		}	
		
	}
// END OF SEARCH
	
}