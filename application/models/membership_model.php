<?php 

/**
 * Membership_model
 * 
 * Validation of login, creation of new users and fetching any user data
 *
 * @author Jahfer Husain
 */

class Membership_model extends CI_Model {

	function validate() {
		$this->db->where('email', $this->input->post('email'));
		$this->db->where('password', md5($this->input->post('password')));
		$query = $this->db->get('userdata');
		
		if($query->num_rows == 1) 
			return true;
	}
	
	function create_member() {
		$new_member_insert_data = array(
			'first_name' => $this->input->post('first_name'),
			'last_name' => $this->input->post('last_name'),
			'email' => $this->input->post('email'),
			'password' => md5($this->input->post('password'))
		);
		
		$this->db->set($new_member_insert_data);		
		$this->db->set('date_created', 'NOW()', FALSE);		
		
		$insert = $this->db->insert('userdata');
		
		return $insert;
		
	}
	
	function fetch_real_name($user_id) {
		$this->db->select('first_name, last_name');
		$this->db->where('id', $user_id);
		$query = $this->db->get('userdata');
		
		if($query->num_rows == 1) {
			$row = $query->row();
			return $row->first_name." ".$row->last_name;
		} else {
			return "Null";
		}		
	}	
	
	function fetch_first_name($user_id) {
		$this->db->select('first_name');
		$this->db->where('id', $user_id);
		$query = $this->db->get('userdata');
		
		if($query->num_rows == 1) {
			$row = $query->row();
			return $row->first_name;
		} else {
			return "Null";
		}		
	}	
	
	function fetch_email($user_id) {
		$this->db->select('email');
		$this->db->where('id', $user_id);
		$query = $this->db->get('userdata');
		
		if($query->num_rows == 1) {
			$row = $query->row();
			return $row->email;
		} else {
			return "Null";
		}
		
	}
	
	function fetch_user_id($email) {
		$this->db->select('id');
		$this->db->where('email', $email);
		$query = $this->db->get('userdata');
		
		if($query->num_rows == 1) {
			$row = $query->row();
			return $row->id;
		} else {
			return "Null";
		}
		
	}
	
	function fetch_dp($user_id, $thumb = FALSE) {
		$this->db->select('dp');
		$query = $this->db->get_where('userdata', array('id'=>$user_id));
		
		if($query->num_rows == 1) {
			$row = $query->row();
			if($thumb)
				return (is_file("userfiles/thumbs/".$row->dp)) ?
					base_url()."userfiles/thumbs/".$row->dp  :
					base_url()."userfiles/thumbs/default.png";
			else
				return (is_file("userfiles/".$row->dp)) ?
					base_url()."userfiles/".$row->dp  :
					base_url()."userfiles/default.png";
		} else {
			return "";
		}
		
	}
	
}