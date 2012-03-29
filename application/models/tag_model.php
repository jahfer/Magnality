<?php 

/**
 * Tag_model
 * 
 * Used to retrieve posts based on tag information
 *
 * @author Jahfer Husain
 */

class Tag_model extends CI_Model {

	function run_tag_search($tags, $offset = 0, $sort = "magic") {		
		$tag_list = explode("+", $tags);
		
		switch($sort) {
			case "old":
				$this->db->order_by("messages.date", "ASC");				
				break;
			case "new":
				$this->db->order_by("messages.date", "DESC");				
				break;
			default:
				$this->db->select('(COUNT(comments.id)+3) / POW(timediff(NOW(), `messages`.`date`)+2, 1.5) AS "sort_order"', FALSE);
				$this->db->order_by("sort_order DESC, time DESC");
		}
	
		$this->db->select('messages.*, UNIX_TIMESTAMP(messages.date) as "time", userdata.first_name, userdata.last_name, userdata.dp, userdata.id AS "user_id", COUNT(comments.id) AS "reply_count"');
		$this->db->join('userdata', 'userdata.id = messages.user');
		$this->db->join('comments', 'messages.id = comments.post_id', 'left');
		$this->db->group_by('messages.id');
		
		// if one tag provided
		if(count($tag_list) == 1) {
			$where = "(
				tags  LIKE '{$tag_list[0]} %'
				OR  tags  LIKE '% {$tag_list[0]} %'
				OR  tags  LIKE '% {$tag_list[0]}'
			)";
			
		   $this->db->where($where);
		   $this->db->or_where('tags', $tag_list[0]);
		} else {
			foreach($tag_list as $tag) {
				$where = "(
					tags  LIKE '$tag %'
					OR  tags  LIKE '% $tag %'
					OR  tags  LIKE '% $tag'
				)";
				
			   $this->db->or_where($where);
			   $this->db->or_where('tags', $tag);
			}
		}
		$this->db->limit(10);		
		$this->db->offset($offset);		
		$query = $this->db->get('messages');			
		return $query;
	}
	
}