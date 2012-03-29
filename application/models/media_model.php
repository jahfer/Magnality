<?php 

/**
 * Media_model
 * 
 * Handles fetching and formatting of media (docs and pics)
 *
 * @author Jahfer Husain
 */

class Media_model extends CI_Model {
	
	function get_media($filename, $id, $resize = FALSE, $square = FALSE) {
		if($filename != "") {
			$ext = strrchr($filename, ".");
			// Is a document
			if($ext == ".pdf")
				return 
				"<p class='doc-link'>
					<img src='".base_url()."img/page_white_text.png'/>
					<a target='_blank' href='".base_url()."userfiles/$filename'>$filename</a>
				</p>";
			else if($ext == ".doc")
				return 
				"<p class='doc-link'>
					<img src='".base_url()."img/page_white_text.png'/>
					<a href='".base_url()."userfiles/$filename'>$filename</a>
				</p>";
				
		// Not a document
			// inline stream image
			else if (!$resize)
				if(!$square)
					return "<a href='".base_url()."userfiles/$filename'><img class='stream-img-thumb' src='".base_url()."userfiles/thumbs/$filename'></a>";
				else {
					$extension_pos = strrpos($filename, '.');
					
					if(!is_file("userfiles/square/$filename")) {	
						$config = array(
							'source_image' => "userfiles/$filename",
							'new_image' => "userfiles/square/",
							'width' => 130,
							'height' => 130,
							'master_dim' => 'rev_auto'
						);
						
						$this->load->library("image_lib", $config);
						$this->image_lib->resize();
						
						//echo $this->image_lib->display_errors();
						
						$config = array(
							'source_image' => "userfiles/square/$filename",
							'maintain_ratio' => FALSE,
							'create_thumb' => FALSE,
							'x_axis' => 0,
							'y_axis' => 0,
							'width' => 130,
							'height' => 130,
							'quality' => '75%'
						);		
						$this->image_lib->initialize($config);
						$this->image_lib->crop();
						
						//echo $this->image_lib->display_errors();
					}	
					return "<a href='".site_url('posts/'.$id)."'><img class='square-img-thumb' width='130' height='130' src='".base_url()."userfiles/square/$filename' ></a>";
				}
			// sidebar image
			else {			
				$extension_pos = strrpos($filename, '.');
				$thumb = substr($filename, 0, $extension_pos) . '_thumb' . substr($filename, $extension_pos);
				
				if(!is_file("userfiles/thumbs/$thumb")) {	
					$config = array(
						'source_image' => "userfiles/thumbs/$filename",
						'create_thumb' => TRUE,
						'width' => 65,
						'height' => 65,
						'master_dim' => 'rev_auto'
					);
					
					$this->load->library("image_lib", $config);
					$this->image_lib->resize();
					
					echo $this->image_lib->display_errors();
					
					$config = array(
						'source_image' => "userfiles/thumbs/$thumb",
						'maintain_ratio' => FALSE,
						'create_thumb' => FALSE,
						'x_axis' => 0,
						'y_axis' => 0,
						'width' => 65,
						'height' => 65,
						'quality' => '75%'
					);		
					$this->image_lib->initialize($config);
					$this->image_lib->crop();
					
					echo $this->image_lib->display_errors();
				}	
				
				return "<a href='".site_url('posts/'.$id)."'><img class='sidebar-img-thumb' width='65' height='65' src='".base_url()."userfiles/thumbs/$thumb'></a>";
			}
		}
	}
	
	function fetch_pics($limit = NULL) {
		$this->db->select('file, id');
		$this->db->where('file !=', "NULL");
		$this->db->like('file', '.png',  'before');
		$this->db->or_like('file', '.gif',   'before');
		$this->db->or_like('file', '.jpg',  'before');
		$this->db->or_like('file', '.jpeg', 'before');		
		$this->db->order_by("id", "DESC");
		if($limit) $this->db->limit($limit);
		$q = $this->db->get("messages");
		
		$pics = array();
		
		foreach($q->result() as $row) {
			array_push($pics, $this->get_media($row->file, $row->id, TRUE));
		}
		
		return $pics;
	}
	
	function fetch_docs() {
		$this->db->select('file, id');
		$this->db->where('file !=', "NULL");
		$this->db->like('file', '.doc',  'before');
		$this->db->or_like('file', '.pdf',   'before');	
		$this->db->order_by("id", "DESC");
		$q = $this->db->get("messages");
		
		$docs = array();
		
		foreach($q->result() as $row) {
			array_push($docs, $this->get_media($row->file, $row->id, TRUE));
		}
		
		return $docs;
	}
	
	function show_media_grid($content) {
		$var = "";
		foreach($content as $item) {
			$var .= $item;
		}
		return $var;
	}
	
}