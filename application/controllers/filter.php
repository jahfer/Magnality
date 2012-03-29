<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Filter
 * 
 * Controller for handling all filter functions
 * such as adding, deleting, editing and loading them
 *
 * @author Jahfer Husain
 */

class Filter extends CI_Controller {

	private $user_id;		
	private $allow_access;
		
	function __construct() {
		parent::__construct();
		$this->is_logged_in();
		$this->user_id = $this->session->userdata('user_id');
		$this->load->model('filter_model');
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
			$this->allow_access = TRUE;
	}
	
	function add() {
		if(!$this->allow_access) return;
		
		$this->filter_model->add_filter();
		redirect($this->input->post('uri'));
	}
	
	function delete($filter_name = NULL) {
		if($this->input->post('filters')) {
			foreach($this->input->post('filters') as $selected_filter) {
				if($this->filter_model->delete_filter($this->user_id, $selected_filter)) {
					$this->session->set_flashdata('filter-delete', '1');	
				} else					
					$this->session->set_flashdata('filter-delete', '2');	
			}
			redirect('edit-filters');
			
		} else if($filter_name) {
			$filter = str_replace(array("+", "_", "-"), array(", ", " ", " "), $filter_name);
			if($this->filter_model->delete_filter($this->user_id, $filter))
				$this->session->set_flashdata('filter-delete', '1');	
			else					
				$this->session->set_flashdata('filter-delete', '2');				
				
		} else
			$this->session->set_flashdata('filter-delete', '2');	
		
		redirect('home');
	}
		
	function edit() {		
		if($this->input->post('data_type') == 'tags')
			$this->filter_model->update_filter_tags();
		else if($this->input->post('data_type') == 'name')
			$this->filter_model->update_filter_name();
			
		echo $this->input->post('new_val');
	}
	
	function show($user_id, $string_name, $sort = "magic", $is_ajax = FALSE, $offset = 0) {
		if(!$this->allow_access) return;
		
		$string_name = str_replace("-", " ", $string_name);
		
		$search_string = $this->filter_model->fetch_search_string($user_id, $string_name);
		
		if(!$search_string)
			show_404('filter');
	
		$this->load->model('tag_model');
		$data['stream_data'] = $this->tag_model->run_tag_search($search_string, $offset, $sort);
		
		$data['search_string'] = str_replace("+", " ", $search_string);
		$this->load->helper('text');
		$this->load->model('post_model');
		
		if($is_ajax)
			echo $this->post_model->print_stream($data['stream_data']);
		else {		
			$data['filter_name'] = $string_name;
			$this->load->model("sidebar_model");
			$this->load->model("filter_model");
			$data['main_content'] = 'stream_ui';
			$this->load->view('inc/template', $data);	
		}	
	}
	
	function edit_settings() {
		if(!$this->allow_access) return;
		
		$data['filters'] = $this->filter_model->fetch_user_filters($this->user_id);
		
		$data['status'] = $this->session->flashdata('filter-delete');
		
		switch($data['status']) {
			case 1:
				$data['status_msg'] = "Filter(s) deleted.";
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
		
		$data['main_content'] = 'filter_settings';
		$this->load->view('inc/template', $data);	
	}
	
	function subscribe() { 
	
		$owner = base64_decode($this->input->post('o'));
		
		if($owner == $this->user_id)
			echo 1;
			
		else {
		
			$action = $this->input->post('action');
			$filter = str_replace("-", " ", base64_decode($this->input->post('f')));
			
			switch($action) {
				case "sub":
					$sub_data = array(				
						"owner_id" 	=> $owner,			
						"user_id" 	=> $this->user_id,					
						"f_name" 	=> $filter
					);
					
					if($this->db->insert("fsub", $sub_data))
						echo 0;
					else
						echo 2;						
					break;
					
				case "unsub":
					$unsub_data = array(				
						"owner_id" 	=> $owner,			
						"user_id" 	=> $this->user_id,					
						"f_name" 	=> $filter
					);
					
					if($this->db->delete("fsub", $unsub_data))
						echo 0;
					else
						echo 2;
					break;
			}
			
		}
		
	}
	
}