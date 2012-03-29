<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Userinfo
 * 
 * Allows the manipulation of user info, such as updating
 * their profile page.
 *
 * @author Jahfer Husain
 */

class Userinfo extends CI_Controller {

	function update_profile() {
		if($this->input->post('upload')) {		
			$this->load->model('upload_model');
			$this->upload_model->upload_display_picture();
		}
		
		$data = array(
			'title' => $this->input->post('title'),
			'about' => $this->input->post('about'),
			'website' => $this->input->post('website')
		);
		
		$user_id = $this->session->userdata('user_id');
		
		$check = $this->db->get_where('userextra', array('user_id' => $user_id));
		
		if($check->num_rows == 0) {
			$data['user_id'] = $user_id;
			if( $this->db->insert('userextra', $data) )				
				$this->session->set_flashdata('profile', '1');			
			else			
				$this->session->set_flashdata('profile', '2');
				
		} else {
			$this->db->where('user_id', $user_id);
			if( $this->db->update('userextra', $data) )
				$this->session->set_flashdata('profile', '1');	
			else			
				$this->session->set_flashdata('profile', '2');				
		}
		
		$email = array('email' => $this->input->post('email'));
		$this->db->where('id', $user_id);
		if( ! $this->db->update('userdata', $email) )
			$this->session->set_flashdata('profile', '2');
		
		redirect('edit-profile');
	}
	
}