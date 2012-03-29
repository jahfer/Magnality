<div id="sub-container" class="clearfix">	
	<div id="permalink-page">
		<h1 class="small-title">User Comments</h1>
		
		<?=$this->post_model->print_stream($post, NULL, FALSE)?>
		
		<ul id="comments">	
			<p class="small">Supported tags: <code>&lt;b&gt;, &lt;u&gt;, &lt;i&gt;</code>. </p>
			<?			
				echo form_open('posts/add_comment', array('class'=>'comment_form'));
				echo form_textarea(array('name' => 'text', 'class' => 'comment_text', 'rows'=>3));
				echo '<br>';
				echo form_submit(array('name' => 'submit', 'value' => 'Reply', 'class' => 'btn primary'));
				echo form_hidden('uri_redirect', $this->uri->uri_string());
				echo form_hidden('post_id', $this->uri->segment(2));
				echo form_hidden('parent', 0);
				echo form_close();		
			?>		
			<?=$comments?>
		</ul>
	</div>
	<div id="permalink-sidebar">
		<h1 class="small-title">Tag History</h1>
		<ul id="tag-history">
			<?=$this->post_model->print_tag_history($history);?>
		</ul>
	</div>
</div>
	
<?$this->javascript->addScript("	
<script>

	$(document).delegate('.remove', 'click', function() {
		var post_id = $(this).siblings('.post_id').html();
		$.post('".site_url('posts/remove')."', {id: post_id}, function(data) {
			window.location = '".site_url('home')."';			
		});	
	});
	
	$(document).delegate('.reply', 'click', function() {
		var parent = $(this).attr('data-parent');
		var reply_form = '<form action=\"".site_url('posts/add_comment')."\" method=\"post\" accept-charset=\"utf-8\" class=\"comment_form\"><textarea name=\"text\" rows=\"3\" class=\"comment_text\" ></textarea><br><input type=\"submit\" name=\"submit\" value=\"Reply\" class=\"btn primary\"  /><input type=\"button\" name=\"cancel\" value=\"Cancel\" class=\"btn cancel\"  /><input type=\"hidden\" name=\"uri_redirect\" value=\"".$this->uri->uri_string()."\" /><input type=\"hidden\" name=\"post_id\" value=\"".$this->uri->segment(2)."\" /><input type=\"hidden\" name=\"parent\" value=\"'+parent+'\" /></form>';
		
		var comment_block = $(this).parentsUntil('.comment_object').siblings('.sub-comments');
		
		if(!$(this).hasClass('replying')) {
			if(comment_block.length < 1)
				$(this).parentsUntil('.comment_object').siblings('.text').parent().append(reply_form);
			else
				comment_block.before(reply_form);
		}
				
		$(this).addClass('replying');
	});

	$(document).delegate('.collapse', 'click', function() {
		if($(this).val() == '[hide]') {
			$(this)
				.val('[show]')
				.addClass('showing')
				.siblings('.text').hide()
				.siblings('.sub-comments').hide();
		} else {
			$(this)
				.val('[hide]')
				.addClass('hiding')
				.siblings('.text').show()
				.siblings('.sub-comments').show();
		}
	});
	
	$(document).delegate('.cancel', 'click', function() {
		$(this).parent().parent().find('.reply').removeClass('replying');
		$(this).parent().html('');
	});
	
	run_tag_js('".site_url('posts/edit_tags')."');

</script>");?>