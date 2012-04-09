<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Login
 * 
 * Used upon credentials submission by form
 * to verify user's account or to create a new one
 * if one does not exist already.
 *
 * @author Jahfer Husain
 */

class Login extends CI_Controller {	
		
	function index() {
		$is_logged_in = $this->session->userdata('is_logged_in');
		
		if(!isset($is_logged_in) || $is_logged_in != true) {	
			$data['main_content'] = 'signup_form';
			$this->load->view('inc/template', $data);
		} else {
			redirect('home');
		}
	}
	
	function validate() {
		$query = $this->membership_model->validate();
	
		if($query) {
			$email = $this->input->post('email');
			$user_id = $this->membership_model->fetch_user_id($email);
			
			$data = array(
				'user_id' => $user_id,
				'is_logged_in' => true
			);
			$this->session->set_userdata($data);
			if($this->input->post('uri-redirect'))			
				redirect($this->input->post('uri-redirect'));
			else
				redirect('home');
			
		} else {		
			$data['login_fail'] = true;
			$data['main_content'] = 'signup_form';
			$this->load->view('inc/template', $data);
		}
	}
	
	function create_member() {
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('first_name', 'First Name', 					'trim|required');
		$this->form_validation->set_rules('last_name', 	'Last Name', 					'trim|required');
		$this->form_validation->set_rules('email', 		'Email Address', 				'trim|required|valid_email|callback_email_check');
		$this->form_validation->set_rules('password', 	'Password', 					'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password2', 	'Password Confirmation', 	'trim|required|matches[password]');
		
		if($this->form_validation->run() == FALSE) {
			$data['main_content'] = 'signup_form';
			$this->load->view('inc/template', $data);
		} else {
			
			if($query = $this->membership_model->create_member()) {
				$data['main_content'] = 'signup_successful';
				$this->load->view('inc/template', $data);
			} else {
				redirect('login');
			}
		}
	}
	
	function email_check($email) {		
		$this->db->select('id');
		$this->db->where('email', $email);
		$query = $this->db->get('userdata');
		
		if($query->num_rows() > 0) {
			$this->form_validation->set_message('email_check', 'There is already an account with this email.');
			return FALSE;		
		} else {	
			return TRUE;
		}
	}	
	
	function logout() {
		$this->session->sess_destroy();
		redirect('login');		
	}
	
}