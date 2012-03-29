<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Chat
 * 
 * Deals with the database submission and retrieval for
 * any chat-based situation where there is a back-and-forth
 * of messages between users.
 *
 * @author Jahfer Husain
 */

class Chat {

	private $CI;
	private $user;
	
	public function __construct() {
		$this->CI  =& get_instance();	
	}

	public function open( $user = "NULL") {
		$this->user = $user;
	}
	
	public function send($req = 'msg') {
		$data = array(
			'usr' => $this->user,
			'msg' => $this->CI->input->post($req)
		);
		
		$this->CI->db->set($data);
		$this->CI->db->set('time', 'NOW()', FALSE);
			 				
		if( $this->CI->db->insert('chat') )
			return "1";
		else
			return "0";
	}
	 
	public function recv() {		
		$this->CI->db->select("*, UNIX_TIMESTAMP(time) as time");
		$q = $this->CI->db->get_where( 'chat', array('id >' => $this->CI->session->userdata('last_msg')) );
		
		$results = $q->result_array();
		
		if( count($results) )
			$this->CI->session->set_userdata('last_msg', $results[count($results)-1]['id']);
			
		// delete old messages
		$this->CI->db->delete('chat', array('TIMEDIFF(NOW(), time) >' => '00:00:30'));
		
		return json_encode($results);
		
	}
	
}

/* End of file Chat.php */