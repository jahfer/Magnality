<?php 

/**
 * Profile_model
 * 
 * Used for retrieval of user data for profile display
 *
 * @author Jahfer Husain
 */

class Profile_model extends CI_Model {

	function load_data($user_id) {		
		$this->db->select('first_name, last_name, dp, UNIX_TIMESTAMP(date_created) as "date_created", email, about, userextra.title AS title_num, titles_lookup.title, website');
		$this->db->join('userextra', 'userdata.id = userextra.user_id', 'LEFT');
		$this->db->join('titles_lookup', 'userextra.title = titles_lookup.id', 'LEFT');
		$this->db->from('userdata');
		$this->db->where('userdata.id', $user_id);
		$query = $this->db->get();
		$profile_data = $query->row();
		
		return $profile_data;
	}
	
	function load_recent_posts($user_id) {		
		$this->db->select('messages.*, UNIX_TIMESTAMP(messages.date) as "time", userdata.first_name, userdata.last_name, userdata.dp, userdata.id AS "user_id", COUNT(comments.id) AS "reply_count"');
		$this->db->from('messages');
		$this->db->where('messages.user', $user_id);
		$this->db->join('userdata', 'userdata.id = messages.user');
		$this->db->join('comments', 'messages.id = comments.post_id', 'left');
		$this->db->group_by('messages.id');
		$this->db->order_by("messages.date", "DESC");
		$this->db->limit(10);
	
		$query = $this->db->get();
		if($query->num_rows() > 0)		
			return $query;
		else
			return false;
	}
	
}