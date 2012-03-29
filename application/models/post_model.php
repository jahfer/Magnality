<?php 

/**
 * post_model
 * 
 * Used to handle the backend of everything outputted in the stream.
 * This includes post and comment submission, deletion, stream data 
 * fetching and output, tag parsing and editing, date calculation 
 * and text formatting/escaping.
 *
 * @author Jahfer Husain
 */

class post_model extends CI_Model {

	private $upload_path;

	function __construct() {
		parent::__construct();
		$this->upload_path = realpath(APPPATH . '../userfiles');	
		//$this->output->enable_profiler(TRUE);
	}

	function fetch_stream($sort="magic", $offset = 0) {
		$this->db->select('messages.*, UNIX_TIMESTAMP(messages.date) as "time", userdata.first_name, userdata.last_name, userdata.dp, userdata.id AS "user_id", COUNT(comments.id) AS "reply_count"');
		switch($sort) {
			case "old":
				$this->db->order_by("messages.date", "ASC");				
				break;
			case "new":
				$this->db->order_by("messages.date", "DESC");				
				break;
			default:
			$this->db->select('(COUNT(comments.id)+1) / POW(timediff(NOW(), `messages`.`date`)+2, 1.5) AS "sort_order"', FALSE);
			$this->db->order_by("sort_order DESC, messages.date DESC");
				$this->db->order_by("messages.date", "DESC");	
		}
		$this->db->from('messages');
		$this->db->join('userdata', 'userdata.id = messages.user');
		$this->db->join('comments', 'messages.id = comments.post_id', 'left');
		$this->db->group_by('messages.id');
		$this->db->limit(10);
		if($offset)	
			$this->db->offset($offset);
	
		$query = $this->db->get();
		if($query->num_rows() > 0)		
			return $query;
		else
			return false;
	}	
	
	function fetch_post_data($post_id) {
		$this->db->select('messages.*, UNIX_TIMESTAMP(messages.date) as "time", userdata.first_name, userdata.last_name, userdata.dp, userdata.id AS "user_id", COUNT(comments.id) AS "reply_count"');
		$this->db->from('messages');
		$this->db->join('userdata', 'userdata.id = messages.user');
		$this->db->join('comments', 'messages.id = comments.post_id', 'left');
		$this->db->group_by('messages.id');
		$this->db->where('messages.id', $post_id);
		
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
			return $query;
		else
			return false;
		
	}
	
	function fetch_comments($post_id, $parent_id = 0, $limit = NULL, &$count = NULL) {
	
		$this->db->select('comments.*, UNIX_TIMESTAMP(comments.date) as "time", userdata.first_name, userdata.last_name, userdata.dp, userdata.id AS "user_id"');
		$this->db->from('comments');
		$this->db->join('userdata', 'userdata.id = comments.user');
		$this->db->order_by("comments.date", "ASC");
		$this->db->where('post_id', $post_id);		
		if($parent_id)	$this->db->where('parent', $parent_id);	
		else $this->db->where('parent', 0);
		$comment_data = $this->db->get();		
		
		$comment_list = "";	
		
		if($comment_data->num_rows() > 0) {
			if($parent_id)	$comment_list .= '<ul class="sub-comments">';
			
			foreach($comment_data->result() as $comment) {			
				if($limit && $count++ >= $limit) break;	
			
				$comment_list .= "<li class='comment_object'>
					<input type=button value='[hide]' class='collapse showing' />
					<p class='meta'>
						<a href='".site_url('users/'.$comment->user_id)."' class='name'>{$comment->first_name} {$comment->last_name}</a><span class='small'> &middot; <span title='{$this->calc_date($comment->time, TRUE)}'>{$this->calc_date($comment->time)}</span> &middot; <input type=button value=Reply class=reply data-parent={$comment->id} data-post-id={$post_id}/>";
						if($this->session->userdata('user_id') == $comment->user_id) 
							$comment_list .= " &middot; ".anchor("posts/remove_comment/{$comment->id}/{$comment->post_id}", 'Remove');
					$comment_list .= "</span></p>
					<p class='text'>{$this->format_text($comment->text)}</p>";
					$comment_list .= $this->fetch_comments($post_id, $comment->id, $limit, $count);
				$comment_list .= "</li>";	
			}
			
			if($parent_id)	$comment_list .= '</ul>';
			
			return $comment_list;
		} else 
			return;
	}
	
	function fetch_tag_history($post_id) {
		$this->db->limit(25);
		$this->db->order_by("id", "DESC");
		$this->db->select("user_id, tags, UNIX_TIMESTAMP(date) as 'time'");
		return $this->db->get_where("tag_history", array("post_id"=>$post_id));
	}
	
	function print_tag_history($data) {
		$out = "";
		foreach ($data->result() as $tag_obj) {
			$tags = explode(" ", $tag_obj->tags);
			$out .= "
			<li class='tag-history-obj'>";
			foreach($tags as $k=>$tag) {
				if($tag != " " && $tag != "")
					$out .= anchor("tags/".$tag, $tag)." ";
				else if($tag_obj->tags == "" || $tag_obj->tags == " ")
					$out .= "<span class='small'>No tags</span>";
			}
			$out .= "<span class='tag-meta small'>".$this->calc_date($tag_obj->time)." by ".anchor("users/".$tag_obj->user_id, $this->membership_model->fetch_real_name($tag_obj->user_id))."</span>
			</li>
			";
		}
		return $out;
	}
	
	function print_stream($stream_data, $highlight = NULL, $show_comments = TRUE) {	
		$dates = array();	
		$remove_link = $tmpl = "";
		
		if(is_object($stream_data)) {
			if($stream_data->num_rows() > 0) {
				foreach($stream_data->result() as $object) {
				
					if(in_array($this->calc_date($object->time), $dates))	
						$object->time = 0;
					$dates[] = $this->calc_date($object->time);
					
					$replies = ($object->reply_count < 2) ? 
						($replies = ($object->reply_count == 0) ? "Reply" : "1 reply") : 
						$object->reply_count." replies";
				
					if($this->session->userdata('user_id') == $object->user_id) 
						$remove_link = "<br><a href='javascript:void(0)' class='remove btn'>Remove</a>";
					else
						$remove_link = "";
					
					$dp = (is_file("userfiles/thumbs/$object->dp")) ? 
							base_url()."userfiles/thumbs/$object->dp" : base_url()."userfiles/thumbs/default.png";
					
					$this->load->model("media_model");
					
					if($highlight) {
						$obj_text = preg_replace("/(".$highlight.")/i", "<mark class=highlight>$1</mark>", $object->text);
					} else
						$obj_text = $object->text;
					
					$comment_count = 0;
						
					$data = array(
						'id' => $object->id,
						'time' => $this->calc_date($object->time),
						'timestamp' => $this->calc_date($object->time, TRUE),
						'permalink' => site_url('posts/'.$object->id),
						'smile_url' => $this->get_user_vote($object->id),
						'smile_vote_panel' => base_url()."img/smile_popup.png",
						'smile_happy' => base_url()."img/smile_happy.png",
						'smile_shock' => base_url()."img/smile_shock.png",
						'smile_sad' => base_url()."img/smile_sad.png",
						'reply_count' => $replies,
						'display_pic' => $dp,
						'emotion' => $this->get_post_vote($object->id),
						'user_id' => site_url('users/'.$object->user_id),
						'full_name' => $object->first_name." ".$object->last_name,
						'tags' => $this->parse_tags($object->tags),
						'post_text' => $this->format_text($obj_text),
						'media_if_available' => $this->media_model->get_media($object->file, $object->id),
						'comment_form_if_available' => "",
						'comments_if_available' => "",
						'remove_link_if_available' => $remove_link
					);
											
					if($show_comments)
						$data['comment_form_if_available'] = 
						
						'<form action="'.site_url("posts/add_comment").'" method="post" accept-charset="utf-8" class="comment_form">
							<textarea name="text" cols="90" rows="3" class="comment_text" placeholder="Add a comment…"></textarea><br>
							<input type="submit" name="submit" value="Reply" class="btn reply-btn"  />
							<input type="hidden" name="uri_redirect" value="/" />	
							<input type="hidden" name="post_id" value="'.$object->id.'" />	
							<input type="hidden" name="parent" value="0" />
						</form>';
												
					if($show_comments) {
						$comm = $this->fetch_comments($object->id, 0, 5, $comment_count);
						if($comm)
							$data['comments_if_available'] = '<ul class="inline-comments" class="clearfix">'.$comm."<a href=".site_url('posts/'.$object->id).">See all replies &raquo;</a></ul>";
					}
						
					
					$tmpl .= $this->parser->parse('post_template', $data, TRUE);
				}
			} #else {
				#return "<p class='error'>No posts found.</p>";
			#}	
		}
		return $tmpl;
	}
	
	
	function add_post() {	
		$user_id = $this->session->userdata('user_id');
		
		$data = array(
			'user' => $user_id,
			'text' => $this->input->post('text'),
			'tags' => $this->input->post('tags')
		);
		
		$config = array(
			'allowed_types' => 'jpg|jpeg|gif|png|doc|pdf',
			'upload_path' => $this->upload_path,
			'max_size' => 4000,
			'max_filename' => 255
		);
		
		$this->load->library('upload', $config);
		if($this->upload->do_upload()) {		
			$image_data = $this->upload->data();
			$data['file'] = $image_data['file_name'];
					
			$config = array(
				'source_image' => $image_data['full_path'],
				'new_image' => $this->upload_path . '/thumbs'
			);		
			
			if($image_data['image_width'] > 400 || $image_data['image_height'] > 400) {	
				$config['width'] = 400;
				$config['height'] = 400;
			}
			
			$this->load->library('image_lib', $config);
			$this->image_lib->resize();
		} else if($data['text'] == "") {
			return;
		}
				
		$this->db->set($data);
		$this->db->set('date', 'NOW()', FALSE);
		$insert = $this->db->insert('messages');			
		
		$post_id = $this->db->insert_id();
		$this->load->model("notification_model");
		$this->notification_model->set_subscription($user_id, $post_id);
		
		$post_data = array(
			"post_id" => $post_id,
			"user_id" => $user_id,
			"tags" => $this->input->post('tags')
		);
		
		$this->db->set($post_data);
		$this->db->set('date', 'NOW()', FALSE);
		$this->db->insert('tag_history');
		
		return $insert;
	}
	
	function add_user_comment($user_id) {
		
		if(!$this->input->post('text'))
			return;
	
		$data = array(
			'post_id' => $this->input->post('post_id'),
			'user' => $user_id,
			'text' => $this->input->post('text'),
			'parent' => $this->input->post('parent')
		);		
		
		$this->db->set($data);
		$this->db->set('date', 'NOW()', FALSE);
		$this->db->insert('comments');
				
		$this->load->model("notification_model");
		$this->notification_model->add_notification($user_id, $data['post_id'], "comment", $data['text']);
	}	
	
	function add_vote($user_id) {
		$post_id = $this->input->post('id');
		$vote = $this->input->post('v');
		if( ! is_numeric($vote) && $vote > 3 ) return;
		
		$q = $this->db->get_where('votes', array('post_id'=>$post_id, 'user_id'=>$user_id));
		
		if ( ! $q->num_rows() ) {			
			$post_data = array(
				"post_id" => $post_id,
				"user_id" => $user_id,
				"vote" => $vote
			);
			
			$this->db->insert('votes', $post_data);
			echo 'Data inserted.';
		} else {
			$this->db->where(array('post_id'=>$post_id, 'user_id'=>$user_id));
			$this->db->update('votes', array('vote'=>$vote));
			echo 'Data updated.';
		}
		
	}
	
	function update_post_tags($post_id, $new_tags) {
			
		$this->db->select("tags");
		$this->db->order_by("id", "DESC");
		$this->db->limit(1);
		$data = $this->db->get_where('tag_history', array('post_id'=>$post_id));
		
		$old = $data->row();
		
		if( ! strcmp($new_tags, $old->tags) )
			return;
			
		$post_data = array(
			"post_id" => $post_id,
			"user_id" => $this->session->userdata('user_id'),
			"tags" => $new_tags
		);
		
		$this->db->set($post_data);
		$this->db->set('date', 'NOW()', FALSE);
		$this->db->insert('tag_history');
		
		$this->db->where('id', $post_id);		
		$this->db->update('messages', array('tags'=>$new_tags));		
	}
	
	function remove_post($user_id) {
		$id = $this->input->post('id');
		$query = $this->db->get_where('messages', array('id'=>$id));
		$row = $query->row();
		if($row->user == $user_id) {
			$this->db->delete('messages', array('id'=>$id));			
			$this->db->delete('notifications', array('post_id'=>$id));
			$this->db->delete('tag_history', array('post_id'=>$id));
		}
	}
	
	function remove_comment($id, $user_id = NULL) {		
		if($user_id) {
			$query = $this->db->get_where('comments', array('id'=>$id));
			$row = $query->row();
			if($row->user != $user_id) 
				return;
		}
		
		$this->db->delete('comments', array('id'=>$id));						
		$q = $this->db->select('id')->get_where('comments', array('parent'=>$id));
		foreach($q->result() as $comment)
			$this->remove_comment($comment->id);
	}
	
	public function calc_date($time, $make_timestamp = FALSE) {	
		
		if($make_timestamp)
			return date('M. j, Y g:ia', $time);
			
		// post from today
		if(date('mjY', $time) == date('mjY'))
			return date('g:ia', $time);
		// post from yesterday
		else if(date('mjY', $time) == date('mjY', time()-86400))
			return "Yesterday";
		// post from this week
		else if(date('WY', $time) == date('WY'))
			return date('l', $time);
		// post from this year
		else if(date('Y', $time) == date('Y'))
			return date('M. jS', $time);
		else if($time == 0)
			return "";
		else					
			return date('M. j, Y', $time);		
	}
	
	function get_user_vote($post_id) {
		$this->db->select('vote');
		$q = $this->db->get_where('votes', array('post_id'=>$post_id, 'user_id'=>$this->session->userdata('user_id')));
		if($q->num_rows() == 1) {
			$user = $q->row();
			switch($user->vote) {
				case 1:
					return base_url()."img/smile_happy.png";
					break;
				case 2:
					return base_url()."img/smile_shock.png";
					break;
				case 3:
					return base_url()."img/smile_sad.png";
					break;
				default:
					return base_url()."img/smile_default.gif";
				
			}
		}
		
		return base_url()."img/smile_default.gif";
	}
	
	function get_post_vote($post_id) {
		$this->db->select('vote, COUNT(vote) AS count');
		$this->db->group_by('vote');
		$this->db->order_by('count', 'DESC');
		$this->db->limit(1);
		$q = $this->db->get_where('votes', array('post_id'=>$post_id));
		$post = $q->row();
		
		if(is_object($post)) {		
			switch($post->vote) {
				case 3:
					return "sad";
					break;
				case 2:
					return "shock";
					break;
				case 1:
					return "happy";
					break;
				default:
					return "";
				
			}
		} 
	}
	
	// @param: $tag_list is raw DB data
	public function parse_tags($tag_list) {
		$HTMLtags = "";
		$char_count = 0;
		
		if($tag_list != null || $tag_list != "") {
			$tags = explode(" ", $tag_list);
			foreach($tags as $tag) {
				if($tag != "")
					$HTMLtags .= anchor('tags/'.$tag, $tag, 'class=tag');
			}
			return $HTMLtags;
		} else return;
	}
	
	function run_search($str, $offset = 0) {
	
		$str = str_replace("+", " ", $str);		
		
		$this->db->select('messages.*, UNIX_TIMESTAMP(messages.date) as "time", userdata.first_name, userdata.last_name, userdata.dp, userdata.id AS "user_id", COUNT(comments.id) AS "reply_count"');
		$this->db->join('userdata', 'userdata.id = messages.user');
		$this->db->join('comments', 'messages.id = comments.post_id', 'left');
		$this->db->group_by('messages.id');
		$this->db->like('messages.text', $str);
		$this->db->or_like('messages.tags', $str);
		
		$this->db->order_by("messages.date", "desc");
	
		$this->db->limit(10);	
		$this->db->offset($offset);
			
		$query = $this->db->get('messages');			
		return $query;		
	}
	
	function format_text($str) {
		$str = nl2br_except_pre(strip_tags($str, '<b><u><i><mark>'));
		
		preg_match('/http:\/\/www.youtube.com\/watch\?v=([A-Za-z0-9_]+)&?(.*)? ?/', $str, $video);		
		if(isset($video[1]))		
			$str .= '<iframe class="youtube-player" type="text/html" width="400" height="285" src="http://www.youtube.com/embed/'.$video[1].'?wmode=opaque" frameborder="0"></iframe>';
		
		return auto_link($str, 'url', TRUE);
	}
	
}