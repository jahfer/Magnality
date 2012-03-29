<?php 

/**
 * Upload_model
 * 
 * Handles all site uploads by users that are *not* considered posts
 *
 * @author Jahfer Husain
 */

class Upload_model extends CI_Model {

	private $upload_path;
	private $user_id;

	function __construct() {
		parent::__construct();
		$this->upload_path = realpath(APPPATH . '../userfiles');		
		$this->user_id = $this->session->userdata('user_id');
	}

	function upload_display_picture() {
		
		// Upload picture to server
		$config = array(
			'allowed_types' => 'jpg|jpeg|gif|png',
			'upload_path' => $this->upload_path,
			'max_size' => 2000
		);	
		$this->load->library('upload', $config);
		if( ! $this->upload->do_upload() )
			return;
		else
			$image_data = $this->upload->data();
		
		// Resize to medium size
		$config = array(
			'source_image' => $image_data['full_path'],
			'width' => 400,
			'height' => 400
		);		
		$this->load->library('image_lib', $config);
		$this->image_lib->resize();		
		
		// Resize to thumbnail		
		$config = array(
			'source_image' => $image_data['full_path'],
			'new_image' => $this->upload_path . '/thumbs',
			'master_dim' => 'rev_auto',
			'width' => 40,
			'height' => 40
		);		
		$this->image_lib->initialize($config);
		$this->image_lib->resize();
		
		$config = array(
			'source_image' => $this->upload_path.'/thumbs/'.$image_data['file_name'],
			'maintain_ratio' => false,
			'x_axis' => 0,
			'y_axis' => 0,
			'width' => 40,
			'height' => 40,
			'quality' => '85%'
		);		
		$this->image_lib->initialize($config);
		$this->image_lib->crop();
		
		
		$this->db->where('id', $this->user_id);
		$this->db->update('userdata', array('dp' => $image_data['file_name']));
		
	}
	
}