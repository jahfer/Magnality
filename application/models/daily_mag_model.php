<?php 

class Daily_mag_model extends CI_Model {

	function __construct() {			
		$this->load->helper('text');		
		$this->load->model("media_model");
	}

	function fetch_discussions($offset = 0) {
		$this->db->select('messages.*, UNIX_TIMESTAMP(messages.date) as "time", CONCAT(userdata.first_name, " ", userdata.last_name) as "author", userdata.id AS "user_id", COUNT(comments.id) AS "reply_count", REPLACE(GROUP_CONCAT(messages.tags SEPARATOR " "), " ", ", ") AS tag_list', FALSE);
			$this->db->from('messages');
			//$this->db->where("DATEDIFF(messages.date, NOW()) >= -7");
			$this->db->join('userdata', 'userdata.id = messages.user');
			$this->db->join('comments', 'messages.id = comments.post_id', 'left');
			$this->db->group_by('messages.id');
			//$this->db->order_by("reply_count", "DESC");
			//$this->db->order_by("messages.date", "DESC");	
			
			$this->db->select('(COUNT(comments.id)+1) / POW(timediff(NOW(), `messages`.`date`)+2, 1.5) AS "sort_order"', FALSE);
			$this->db->order_by("sort_order DESC, messages.date DESC");
			
			$this->db->limit(5);
			$this->db->offset($offset);
					
			$query = $this->db->get();
			
			if($query->num_rows() > 0)	{
				$posts = $query->result_array();
				
				$this->load->model('post_model');		
				
				foreach($posts as $k=>&$post) {		
					$post['comments'] = $this->post_model->fetch_comments($post['id']);
					if ($post['comments'] == "")
						$post['comments'] = "No comments posted.";
					
					$post['timestamp'] = $this->post_model->calc_date($post['time']);
					
					$post['comment_url'] = site_url("posts/add_comment");
					
					if($post['file'])
						$post['image_if_available'] = $this->media_model->get_media($post['file'], $post['id']);
					else
						$post['image_if_available'] = "";
						
					$post['tags'] = $this->post_model->parse_tags($post['tags']);
				}
				
				return $posts;
			}
			else return false;
	}
	
	function fetch_tag_concat() {		
		$this->db->select('COUNT(comments.id) AS "reply_count", GROUP_CONCAT(DISTINCT messages.tags SEPARATOR " ") AS tag_list', FALSE);
			$this->db->from('messages');
			$this->db->where("DATEDIFF(messages.date, NOW()) >= -7");
			$this->db->join('comments', 'messages.id = comments.post_id', 'left');
			
			$this->db->select('(COUNT(comments.id)+1) / POW(timediff(NOW(), `messages`.`date`)+2, 1.5) AS "sort_order"', FALSE);
			$this->db->order_by("sort_order DESC, messages.date DESC");
					
			$query = $this->db->get();
			if($query->num_rows() > 0)	{
				$data = $query->row();
								
				return "Including: ".word_limiter(implode(", ", array_unique(explode(" ", $data->tag_list))), 10);
				
			} else
				return "";
	}
	
	function fetch_pics($limit = NULL) {
		$this->db->select('file, id');
		$this->db->where("DATEDIFF(date, NOW()) >= -7");	
		$this->db->where('file !=', "NULL");
		$this->db->where("(file  LIKE '%.png'
		OR  file  LIKE '%.gif'
		OR  file  LIKE '%.jpg'
		OR  file  LIKE '%.jpeg')");
			
		$this->db->order_by("date", "DESC");
		if($limit) $this->db->limit($limit);
		$q = $this->db->get("messages");
		
		if($q->num_rows() > 0)	{
		
			$pic_data = array();
			
			foreach($q->result() as $row) {
				array_push($pic_data, $this->media_model->get_media($row->file, $row->id, FALSE, TRUE));
			}
			
			return $this->media_model->show_media_grid($pic_data);
			
		} else
			return "No new pictures.";
	}
	
	function fetch_docs($limit = NULL) {
		$this->db->select('file, id');
		$this->db->where("DATEDIFF(date, NOW()) >= -7");	
		$this->db->where('file !=', "NULL");
		$this->db->where("(file  LIKE '%.doc' OR  file  LIKE '%.pdf')");
			
		$this->db->order_by("date", "DESC");
		if($limit) $this->db->limit($limit);
		$q = $this->db->get("messages");
		
		if($q->num_rows() > 0)	{
		
			$docs = array();
			
			foreach($q->result() as $row) {
				array_push($pics, $this->media_model->get_media($row->file, $row->id, FALSE, TRUE));
			}
			
			return $docs;
		} else 
			return "No new documents.";
	}
	
	function fetch_links() {
		$this->db->select("text, CONCAT('".site_url("posts")."/', id) AS post_url");
		$this->db->from('messages');
		$this->db->where("text REGEXP 'http://[a-zA-Z\#\?\/\-\_]\.[a-zA-Z\#\?\/\-\_]'");
		$this->db->where("DATEDIFF(messages.date, NOW()) >= -7");
		
		$this->db->order_by("date", "DESC");
		$this->db->limit(5);
				
		$query = $this->db->get();
		if($query->num_rows() > 0)	{	
			$links = $query->result_array();
			$link_str = "";
			foreach($links as &$link) {
				
				$link_str .= "
				<tr>
					<td class='perma_hash'><a href='{$link['post_url']}'>#</a></td>
					<td class='link_row'>".auto_link($link['text'], 'url', TRUE)."</td>
				</tr>";
			}
			
			return $link_str;
			
		} else
			return "No new links.";
	}
	
	function fetch_notifications() {
		
		$this->load->model('notification_model');
		$n_data = $this->notification_model->fetch_notifications(TRUE);
		$n_result = $this->notification_model->show_list($n_data);
		if ($n_result == "") $n_result = "No new notifications.";
		
		return $n_result;
	}
	
}