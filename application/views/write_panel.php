<div id="write_block" class="clearfix">
	<img id=write_dp width=40 height=40 src="<?=$this->membership_model->fetch_dp($this->session->userdata('user_id'), TRUE);?>" />
	<?php
		echo form_open_multipart('posts/add', 'id=write_form');		
		echo form_textarea(array('name' => 'text', 'id' => 'post-text', 'rows'=>3, 'placeholder'=>'What\'s up?'));
	?>
		<span class="hidden" id="post-extra">
	<?php
		echo form_input(array('name' => 'tags', 'id' => 'post-tags', 'placeholder'=>'+ Add tags to this post'));
		
		echo '<br><a href="javascript:void(0)" id="attach-link" class="btn">Attach a file&hellip;</a>';
		echo '<div id="attachment"><p class="small">Attach (.png, .jpg, .jpeg, .gif, .doc, .pdf)</p>';
		echo form_upload(array('name' => 'userfile', 'id' => 'form-attachment')).'<br>';
		echo '</div>';
		echo form_submit(array('name' => 'submit', 'value' => 'Share', 'class' => 'btn primary'));
		echo form_close();
	?>			
		</span>
	</div>