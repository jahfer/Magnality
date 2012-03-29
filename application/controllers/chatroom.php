<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Chatroom
 * 
 * Controller for sending and receiving data for the chatroom
 *
 * @author Jahfer Husain
 */

class Chatroom extends CI_Controller {

	function index() {		
		$this->load->view('chatroom');
	}
	
	function getJSON() {		
		$user_id = $this->session->userdata('user_id');
		$name = $this->membership_model->fetch_real_name($user_id);
		// Sets username for chat
		$this->chat->open($name);
		// JSON encoded
		echo $this->chat->recv();
	}
	
	function set() {		
		$user_id = $this->session->userdata('user_id');
		$name = $this->membership_model->fetch_real_name($user_id);
		$this->chat->open($name);
		if( $this->input->post('msg') )
			// returns status of sending 
			echo $this->chat->send();
	}
	
}