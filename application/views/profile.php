
<div id="sub-container" class="clearfix">
	<div id="profile">
	
		<div id="profile_info">
			<?$dp=(is_file("userfiles/".$profile->dp)) ? $profile->dp : "default.png"?>
			<img id='profile-img' src="<?=base_url()."userfiles/".$dp?>" />
			<h2 class="large-title" style="margin-top:20px"><?=$profile->first_name?> <?=$profile->last_name?></h2>
			
			<? if($this->uri->segment(2) == $LOGGED_USER_ID) : ?>
			<?=anchor('edit-profile', 'Edit Profile', array('class'=>'btn btn-right', 'id'=>'edit_profile_link'));?>
			<? endif; ?>
			
			<? if($profile->about != "") : ?>
			<p class="small">About</p>
			<p><?=$profile->about?></p>
			
			<? endif; if($profile->title != "") : ?>
			<p class="small">Title</p>
			<p><?=$profile->title?></p>
			
			<? endif; if($profile->website != "") : ?>
			<p class="small">Website</p>
			<p><?=auto_link(prep_url($profile->website), 'url', TRUE)?></a></p>
			
			<? endif; if($profile->email != "") : ?>
			<p class="small">Email</p>
			<p><?=preg_replace('/@/', '@<span class="visuallyhidden">null</span>',  $profile->email)?></p>
			<? endif; ?>
			
				<? 
					if($filters) {
						
						echo '<p class="small">Filters</p>
						<ul id="user_filter_list">';
						
						if($filters->num_rows() > 0) {
							foreach ($filters->result() as $filter) {
								$formatted_name = str_replace(" ", "-", $filter->name);
								echo("<li>".anchor("filter/".$user_id."/".$formatted_name, $filter->name)."</li>");
							}
						}
						echo '</ul>';
					}
				?>
			
		</div>
		<div id="user_stream">
			<?=$this->post_model->print_stream($stream_data);?>
		</div>
		
	</div>
</div>

<? $this->javascript->addScript("
<script>    
    $(document).delegate('.remove', 'click', function() {
    	var post_id = $(this).siblings('.post_id').html();
    	$(this).parentsUntil('#user_stream').fadeOut('fast');
    	$.post('".site_url('posts/remove')."', {id: post_id});
    });
    
    
    run_tag_js('".site_url('posts/edit_tags')."');
</script>
"); ?>