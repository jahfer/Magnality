<?php 

/**
 * Notification_model
 * 
 * All situations regarding notifications on the backend including: 
 * fetching notifications, setting new subscriptions and notifications, 
 * marking as read and outputting the notification panel.
 *
 * @author your name
 * @tag value
 */

class Notification_model extends CI_Model {

	function fetch_notifications($new_only = FALSE) {	
		$user_id = $this->session->userdata('user_id');		
		
		// select read notifications
		if($new_only) {
			$this->db->select('notif_id');
			$this->db->where('user', $user_id);
			$read_check = $this->db->get('read_notifications');
			$read_list = array();
			foreach($read_check->result() as $read_item)
				array_push($read_list, $read_item->notif_id);
			$are_read = (count($read_check->result_array())) ? TRUE : FALSE;
		}
	
		// select all notifications
		$this->db->select("messages.id, messages.user AS author, notifications.type, 
								MAX(UNIX_TIMESTAMP(notifications.time)) as time, 
								GROUP_CONCAT(DISTINCT notifications.trigger) AS triggers");
		$this->db->from('messages');
		$this->db->join('notifications', 'notifications.post_id = messages.id');
		$this->db->join('subscriptions', 'notifications.post_id = subscriptions.post_id');
		$this->db->where('subscriptions.user', $user_id);
		$this->db->where('notifications.trigger !=', $user_id);
		$this->db->where("subscriptions.time < `notifications`.`time`");
		
		if($new_only && $are_read) $this->db->where_not_in('notifications.id', $read_list);
		
		$this->db->group_by('notifications.type, notifications.post_id');
		$this->db->order_by('notifications.time', 'DESC');
		
		$query = $this->db->get();
		
		if($query->num_rows() > 0)
			return $query;
		else
			return FALSE;
	}
	
	/**
	* @param user_id: The unique ID of the user who caused the action
	* @param post_id: The unique ID of the post to which this notification is directed
	* @param type:		The specified type of the post (max 10 chars)
	*/
	function add_notification($user_id, $post_id, $type = "comment", $comment=NULL) {		
		$notify_data = array(
			'trigger' => $user_id,
			'post_id' => $post_id,
			'type' 	 => $type
		);	
		$this->db->set('time', 'NOW()', FALSE);
		$this->db->insert('notifications', $notify_data);
		
		$this->notification_model->set_subscription($user_id, $post_id);
		
// send out email
		
		switch($type) {
			case "comment": 	$verb = "commented on"; break;
			case "tag":			
			default:				return;
		}
		
		$this->load->library('email');		
	
		$trigger = $this->membership_model->fetch_real_name($user_id);
		$trigger_f = $this->membership_model->fetch_first_name($user_id);
		//$message_data = $this->db->get_where('messages', array('id'=>$post_id));
		//$message = $message_data->row();
		
		//if( ! is_object($message) ) {
			$message_data = $this->db->get_where('comments', array('id'=>$post_id));
			$message = $message_data->row();
		//}
			
		if( ! is_object($message) ) return;
		
		switch($message->user) {
			case $user_id:
				$author = $author_link = "their own";
				break;
			case $user_id:
				$author = $author_link = "your";						
				break;
			default:
				$author = $this->membership_model->fetch_real_name($message->user);
				
		}
		
		$this->load->model("post_model");
		//$time = $this->post_model->calc_date(date("U"));		
		
		$to      = '';
		$subject = $this->membership_model->fetch_real_name($user_id)." $verb $author's post.";
		
		$message = "$trigger $verb $author's post.";
		$message .= "<br/><br/>&ldquo;".nl2br($comment)."&rdquo;";
		$message .= "<br/><br/>".anchor("posts/$post_id", "View Post on Magnality");
		$message .= "<br/><br/>Thanks,<br/>The Magnality Team";
		
		$header  = "MIME-Version: 1.0" . "\r\n";	
		$header .= "Reply-To: Magnality <do_not_reply@magnality.net>\r\n"; 
		$header .= "Return-Path: Magnality <do_not_reply@magnality.net>\r\n"; 
		$header .= 'From: Magnality <jahferco@box384.bluehost.com>' . "\r\n";
		$header .= "Organization: Magnality.net\r\n"; 
		$header .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";	
		
		$this->db->select("email");
		$this->db->join("userdata", "subscriptions.user = userdata.id");
		$this->db->join("userextra", "userdata.id = userextra.user_id");
		$data = $this->db->get_where("subscriptions", array('post_id'=>$post_id, 'userdata.id !='=>$user_id, 'emails'=>1));
		$header .= 'Bcc: ';
		foreach($data->result() as $user)		
			$header .= "'$user->email', ";			
		$header .= "\r\n";
		
		mail($to, $subject, $message, $header);	
		
		
			
#		$this->email->from('do_not_reply@magnality.net', 'Magnality');
#		$this->email->to('jahferh@gmail.com');
		//$this->email->bcc($subscribers);
		
#		$this->email->subject("$trigger $verb $author post");
#		$this->email->message("$trigger $verb $author post.\n".anchor("posts/$post_id", "Click here to view &raquo;").".\n\n<hr/>\n\n<a href='http://magnality.net'>magnality.net</a>");	
		
#		$this->email->send();
#		echo $this->email->print_debugger();
	}
	
	function set_subscription($user_id, $post_id) {		
		$notify_data = array(
			'user' => $user_id,
			'post_id' => $post_id
		);	
		
		// check if already subscribed
		$test = $this->db->get_where('subscriptions', array('user'=>$user_id, 'post_id'=>$post_id));
		
		if($test->num_rows == 0) {
			$this->db->set('time', 'NOW()', FALSE);		
			$this->db->insert('subscriptions', $notify_data);	
		}
	}
	
	function mark_as_read($user_id) {	
		
		// select read notifications
		$this->db->select('notif_id');
		$this->db->where('user', $user_id);
		$read_check = $this->db->get('read_notifications');
		$read_list = array();
		foreach($read_check->result() as $read_item)
			array_push($read_list, $read_item->notif_id);
		$are_read = (count($read_check->result_array())) ? TRUE : FALSE;
	
		// select all notifications
		$this->db->select("notifications.id");
		$this->db->from('notifications');
		$this->db->join('subscriptions', 'notifications.post_id = subscriptions.post_id');
		$this->db->where('subscriptions.user', $user_id);
		$this->db->where('notifications.trigger !=', $user_id);		
		if($are_read) $this->db->where_not_in('notifications.id', $read_list);		
		$query = $this->db->get();				
		
		foreach($query->result() as $unread) {
			$data = array(
				'notif_id' => $unread->id,
				'user' => $user_id
			);
			$this->db->set($data);
			$this->db->set('time', 'NOW()', FALSE);
			$this->db->insert('read_notifications');
		}
	}
	
	function get_unread_count() {
		$query = $this->fetch_notifications(TRUE);
		if($query)
			return $query->num_rows();
		else
			return 0;
	}
	
	function show_list($query) {
		
		if(!$query) {
			return;
		}
		
		$user_id = $this->session->userdata('user_id');			
		
		$res = "";
	
		foreach($query->result() as $row) {
			$trigger_array = explode(",", $row->triggers);
		
			switch(count($trigger_array)) {		
				case 1:
					$trigger = anchor('users/'.$row->triggers, $this->membership_model->fetch_real_name($row->triggers));
					break;
				case 2:
					foreach($trigger_array as $k=>$v) {
						$trigger_link[$k] = anchor('users/'.$v, $this->membership_model->fetch_real_name($v));	
					}
					$trigger = $trigger_link[0]." and ".$trigger_link[1];
					break;
				default:
					$trigger = count($trigger_array)." people";
			}
			
			switch($row->author) {
				case $row->triggers:
					$author = "their";
					break;
				case $user_id:
					$author = "your";						
					break;
				default:
					$author = anchor("users/".$row->author, $this->membership_model->fetch_real_name($row->author))."'s";
			}
				
			switch($row->type) {
				case "comment":
					$verb = "commented on";
					break;
				case "tag":
					$verb = "edited tags on";
					break;
			}
			
			$this->load->model("post_model");
			$time = $this->post_model->calc_date($row->time);
		
			$res .= "<li class='type_$row->type'>$trigger $verb $author ".anchor("posts/".$row->id, "post").".<br><span class='small'>$time</span></li>";
		}
		
		return $res;
	}
	
	function load_notifications() {		
		
		$user_id = $this->session->userdata('user_id');
		
		$this->load->helper('text');
		$this->load->model("notification_model");
		$query = $this->notification_model->fetch_notifications( TRUE /*new only*/ ); 
			
		echo '<div class="sidebar_show_all">';
		if($query)
			echo "<a href='javascript:void(0)' id=mark_notifications>Mark as Read</a> &middot; ";
		echo anchor('notifications', "View all &raquo;");
		echo '</div>';
			
		echo "<h1 class='small-title'>Notifications</h1>";			
		echo '<ul id="notifications">';
			if($query)
				echo $this->notification_model->show_list($query);
			else
				echo "No new notifications.";
		echo '</ul>';
		
		echo "";
	}
	
}