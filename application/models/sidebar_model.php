<?php 

/**
 * Sidebar_model
 * 
 * Generates output for each module in the sidebar of the home page
 *
 * @author Jahfer Husain
 */

class Sidebar_model extends CI_Model {
	
	function load_search() {		
		echo form_open('posts/search_redirect', 'id="search_form"');		
		echo form_input(array('type'=>'search', 'name' => 'site-search', 'class'=>'glow', 'id' => 'site-search', 'placeholder' => 'Search the site'));
		echo form_submit(array('name' => 'submit', 'value' => 'Go', 'class' => 'ir', 'id'=>'go'));
		echo form_close();
	}
	
	function load_tips() {
		$str[0] = "Tag your post with words like \"Question\" or \"Link\" so others are easily able to find it.";
		$str[1] = "Create your own custom filters by clicking the \"+ Add Filter\" button in the filter bar above.";
		$str[2] = "To see all of the replies from a post, click the link under the timestamp of the post.";
		$rand = rand(0, 2);
	
		echo "
		<div class='sidebar-object sub'>
			<h1 class='small-title'>Tips</h1>
			<div id='tips'>
				{$str[$rand]}
			</div>
		</div>";
	}

	
	function load_pics( $page, &$stream_data ) {
		$this->load->model("media_model");
			
		$this->db->select('file, id');
		$this->db->where('file !=', "NULL");
		
		$where = "(file  LIKE '%.png' OR file LIKE '%.gif' OR file LIKE '%.jpg' OR file LIKE '%.jpeg')";		
		$this->db->where($where);
		
				
		if($page != "home" && $page != "") {				
			$stream_ids = array();
		
			foreach($stream_data->result() as $object) {
				array_push($stream_ids, $object->id);
			}
			
			if( count($stream_ids) > 0 )
				$this->db->where_in('id', $stream_ids);
			else
				$this->db->where_in('id', 0);						
		}
					
		$this->db->order_by("id", "DESC");
		$q = $this->db->get("messages");
				
		if($q->num_rows() < 1) return;
		
		$count = 1;
		
		echo '<div class="sidebar-object">';
			echo '<h1 class="small-title">Related Pics</h1>';
			echo anchor('pics', "See All", 'class=sidebar_show_all');
				
			foreach($q->result() as $row) {
				echo $this->media_model->get_media($row->file, $row->id, TRUE);
				if($count++ > 7) break;
			}		
		echo '</div>';
	}
	
	function load_docs( $page, &$stream_data ) {
		$this->load->model("media_model");
		
			$this->db->select('file, id');
			$this->db->where('file !=', "NULL");
			
			$where = "(file  LIKE '%.pdf' OR file LIKE '%.doc')";		
			$this->db->where($where);
			
			if( $page != "home" && $page != "" ) {
				$stream_ids = array();
			
				foreach($stream_data->result() as $object) {
					array_push($stream_ids, $object->id);
				}
				
				if( count($stream_ids) > 0 )
					$this->db->where_in('id', $stream_ids);
				else
					$this->db->where_in('id', 0);				
			}
				
			
			$this->db->order_by("id", "DESC");
			$q = $this->db->get("messages");
			
			if($q->num_rows() < 1) return;
			
			$count = 1;
			
			echo '<div class="sidebar-object sub">';
				echo '<h1 class="small-title">Related Documents</h1>';
				echo anchor('docs', "See All", 'class=sidebar_show_all');
				
				echo '<div id="doc-container" class="clearfix">';
				
				foreach($q->result() as $row) {
					echo $this->media_model->get_media($row->file, $row->id);
					if($count++ > 4) break;
				}
			echo '</div>';
		echo '</div>';
	}
	
	
}