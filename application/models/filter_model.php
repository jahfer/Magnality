<?php 

/**
 * Filter_model
 * 
 * All backend setting/getting for a user-defined filter.
 * This includes adding, deleting, updating and outputting filters.
 *
 * @author Jahfer Husain
 */

class Filter_model extends CI_Model {

	function fetch_search_string($user_id, $name) {
		$this->db->select('filter_string');
		$this->db->where('user_id', $user_id);
		$this->db->where('name', $name);
		$query = $this->db->get('filters');
		
		if($query->num_rows == 1) {
			$row = $query->row();
			
			return $row->filter_string;
			
		} else {
			return NULL;
		}
	}	
	
	function fetch_user_filters($user_id) {
		
		$this->db->select('name, filter_string, id, user_id');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get('filters');
		
		if($query->num_rows() > 0)
			return $query;
		else
			return FALSE;
	}
	
	function print_user_filters() {
		$user_id = $this->session->userdata('user_id');
		$this->db->select('name, filter_string');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('id', 'ASC');
		$query = $this->db->get('filters');	
		
		$cur_page_user 	= $this->uri->segment(2);
		$cur_page_filter	= $this->uri->segment(3);
		$logged_in_user 	= $user_id;
		
		$filter_list = "";
			
		if($query->num_rows() > 0) {
			foreach($query->result() as $filter) {	
				$formatted_name = str_replace(" ", "-", $filter->name);			
				$is_active = ($cur_page_user == $logged_in_user && $cur_page_filter == $formatted_name) ? " class='active'": "" ;	
							
				$filter_list .= "<li$is_active>".anchor("filter/$logged_in_user/$formatted_name", $filter->name)."</li>";
			}			
			
		}
							
		$this->db->select('filters.user_id, name, filter_string, fsub.id');
		$this->db->join('fsub', 'filters.user_id = fsub.owner_id AND filters.name = fsub.f_name');
		$this->db->where('fsub.user_id', $user_id);
		$this->db->order_by("id", "asc");
		$query = $this->db->get('filters');
		
		if($query->num_rows() > 0) {
			
			foreach($query->result() as $filter) {		
				$formatted_name = str_replace(" ", "-", $filter->name);			
				$is_active = ($cur_page_filter == $formatted_name) ? " class='active'": "" ;	
							
				$filter_list .= "<li$is_active>".anchor("filter/$filter->user_id/$formatted_name", $filter->name)."</li>";
			}			
			
		}
		
		echo $filter_list;
	}	
	
	function add_filter() {
		$data['name'] = $this->input->post('title');
		$tags = $this->input->post('tags');
		
		if($data['name'] && $tags) {
		
			$data['filter_string'] = str_replace(" ", "+", $tags);
			
			$this->load->model('membership_model');
			$user_id = $this->session->userdata('user_id');
			$data['user_id'] = $user_id;
			
			$this->db->insert('filters', $data);
		} else 
			return;
	}
	
	function delete_filter($user_id, $name) {
		$this->db->where('user_id', $user_id);
		$this->db->where('name', $name);
		if($this->db->delete('filters'))
			return TRUE;
		else
			return FALSE;
	}
	
	function is_owner($user_id) {
		if($this->session->userdata('user_id') == $user_id)
			return TRUE;
		else
			return FALSE;
	}
	
	function update_filter_tags() {
		$new_tags = str_replace(" ", "+", $this->input->post('new_val'));
		$id = $this->input->post('filter_id');	
		$data = array('filter_string' => $new_tags);		
		$this->db->update('filters', $data, "id = $id");
	}
	
	function update_filter_name() {
		$id = $this->input->post('filter_id');	
		$data = array('name' => $this->input->post('new_val'));		
		$this->db->update('filters', $data, "id = $id");		
	}
	
	function _check_if_subscribed($owner = NULL, $name = NULL) {
		if($owner !== NULL && $name !== NULL) {			
			$sub = array(
				'user_id' 	=> $this->session->userdata('user_id'),
				'owner_id' 	=> $owner,
				'f_name'		=> $name
			);
		} else {		
			$sub = array(
				'user_id' 	=> $this->session->userdata('user_id'),
				'owner_id' 	=> $this->uri->segment(2),
				'f_name'		=> str_replace("-", " ", $this->uri->segment(3))
			);
		}
		
		$q = $this->db->get_where("fsub", $sub);
		if ($q->num_rows() == 0)
			return FALSE;
		else
			return TRUE;
	}
	
}