</div>
<div id="filter-bar" class="clearfix">
		<ul id="filters" class="container">
			<li id="today"><a href='<?=site_url("/")?>'>My Mag</a></li><?
				$this->filter_model->print_user_filters();
				$verb = " tagged ";
				$page = $this->uri->segment(1);
				if($page == "tags") : 
				?><li class='active'><?=anchor('tags/'.$tag_url, "Tag: &#8220;".character_limiter($search_string, 15)."&#8221;");?></li>
			<? elseif($page == "search") : $verb = " containing "; 
			?><li class='active'><?=anchor('search/'.$search_url, "Search: &#8220;".character_limiter($search_string, 15)."&#8221;");?></li>		
			<? endif; ?><li id="add-filter" ><a href='javascript:void(0)' title='Add filter' id="add-button">+ Add Filter</a></li>
		</ul>
</div>
<div class="container main clearfix">
<div id="stream">
	<?$this->load->view('write_panel');?>
	<div id="stream-list">	
		<? if($page == "filter") : ?>
		<div id="stream-sort">
			<a href="#" class="ir" id="stream-settings-btn">Settings</a>
			<ul id="stream-sort-list">
				<?php 
					$active = 'class="active"';
					$path = "{$this->uri->segment(1)}/{$this->uri->segment(2)}/{$this->uri->segment(3)}/";
					$sort = $this->uri->segment(4);
				?>
					
				<? switch ($sort) :
					case "old": ?>
					<li><?=anchor($path.'magic', 'Magic')?></li>
					<li class="active"><?=anchor($path.'old', 'Oldest First')?></li>
					<li><?=anchor($path.'new', 'Newest First')?></li>
				<? break; ?>
				<? case "new": ?>					
					<li><?=anchor($path.'magic', 'Magic')?></li>
					<li><?=anchor($path.'old', 'Oldest First')?></li>
					<li class="active"><?=anchor($path.'new', 'Newest First')?></li>
				<? break; ?>
				<? default: ?>		
					<li class="active"><?=anchor($path.'magic', 'Magic')?></li>
					<li><?=anchor($path.'old', 'Oldest First')?></li>
					<li><?=anchor($path.'new', 'Newest First')?></li>				
				<? endswitch ?>
			</ul>
		</div>
		<? endif; ?>
		<ul>
		<?=
			isset($search_string) ? 
				$this->post_model->print_stream($stream_data, $search_string) : 
				$this->post_model->print_stream($stream_data);
		?>		
		</ul>
	</div>	
	
	<div id="load-more" data-offset="10">
		Load more
	</div>
		
</div>

<div id="sidebar">
	<?php	
	
		$this->sidebar_model->load_search(); 
		
		echo "<div id=page-info class='sidebar-object clearfix'>";
			// if is a filter page
			if( $page == "filter" ) {
				$filter_user_id = $this->uri->segment(2);
				// if filter page is not *your* filter
				if($filter_user_id != $this->session->userdata('user_id')) {
					echo "<b class=small-title>";
					echo anchor("users/$filter_user_id", $this->membership_model->fetch_real_name($filter_user_id))." / ".$filter_name;
					echo "</b>";
				} else
					echo "<b class=small-title>".anchor("users/$filter_user_id", "Me")." / ".$filter_name."</b>";
			
				if( $this->filter_model->is_owner($this->uri->segment(2)) ) {
					echo anchor("filter/delete/".$this->uri->segment(3), "Delete this filter", "id=delete-filter"); 
				} else {
					if($this->filter_model->_check_if_subscribed() == TRUE)
						echo anchor('#', 'Unsubscribe', array('class'=>'btn unsubscribe'));
					else
						echo anchor('#', 'Subscribe', array('class'=>'btn subscribe'));
					$this->javascript->addScript("
						<script>								
							$('.subscribe').click(function(e){
								sub('".site_url('filter/subscribe')."', $(this), 'sub','".base64_encode($this->uri->segment(2))."', '".base64_encode($this->uri->segment(3))."');
								e.preventDefault();
							});
							
							$('.unsubscribe').click(function(e){
								sub('".site_url('filter/subscribe')."', $(this), 'unsub','".base64_encode($this->uri->segment(2))."', '".base64_encode($this->uri->segment(3))."');
								e.preventDefault();
							});
						</script>
					");
				}
			}
			
			if( isset($search_string) ) {
				if($page == "tags" || $page == "filter") {						
					echo "<p id='filter-desc'>Showing all posts tagged ";
					foreach(explode(" ", $search_string) as $tag)
						echo anchor('tags/'.$tag, $tag, 'class=tag');
				}
				else if($page == "search") {
					echo "<p id='filter-desc'>Showing all posts $verb<span class='highlight'>".$search_string."</span> or tagged ";
					foreach(explode(" ", $search_string) as $tag)
						echo anchor('tags/'.$tag, $tag, 'class=tag');
				} 
				else {
					echo ".";
				}
			}
		echo "</div>";
			
		
		echo $this->sidebar_model->load_pics( $page, &$stream_data );
		echo $this->sidebar_model->load_docs( $page, &$stream_data );
		
		if(isset($filter_list)) echo $filter_list."</ul>";
		
		$this->sidebar_model->load_tips( $page, &$stream_data );
	?>
</div>
</div>

<?
switch($this->uri->segment(1, 0)) {
	case 'filter':
		if($this->uri->segment(4, 0))
			$ajax_url = site_url($this->uri->uri_string().'/ajax');
		else
			$ajax_url = site_url($this->uri->uri_string().'/magic/ajax');			
		break;
	case 'tags':
		$ajax_url = site_url($this->uri->uri_string().'/ajax');
		break;		
	case 'search':
		$ajax_url = site_url($this->uri->uri_string().'/ajax');
		break;
	case 0:					
		$ajax_url = site_url('posts/load/'.$this->uri->segment(2,"magic"));
}		


$this->javascript->addScript("	

	<script>
	$(document).delegate('.reply', 'click', function() {
		var parent = $(this).attr('data-parent');
		var post_id = $(this).attr('data-post-id');
		var reply_form = '<form action=\"".site_url('posts/add_comment')."\" method=\"post\" accept-charset=\"utf-8\" class=\"comment_form\"><textarea name=\"text\" rows=\"3\" class=\"comment_text\" ></textarea><br><input type=\"submit\" name=\"submit\" value=\"Reply\" class=\"btn primary\"  /><input type=\"button\" name=\"cancel\" value=\"Cancel\" class=\"btn cancel\"  /><input type=\"hidden\" name=\"post_id\" value=\"'+post_id+'\" /><input type=\"hidden\" name=\"parent\" value=\"'+parent+'\" /></form>';
		
		var comment_block = $(this).parentsUntil('.comment_object').siblings('.sub-comments');
		
		if(!$(this).hasClass('replying')) {
			if(comment_block.length < 1)
				$(this).parentsUntil('.comment_object').siblings('.text').parent().append(reply_form);
			else
				comment_block.before(reply_form);
		}
				
		$(this).addClass('replying');
	});
	
	$(document).delegate('.cancel', 'click', function() {
		$(this).parent().parent().find('.reply').removeClass('replying');
		$(this).parent().html('');
	});

	$(document).delegate('.remove', 'click', function() {
		var post_id = $(this).siblings('.post_id').html();
		$(this).parentsUntil('ul').fadeOut('fast');
		$.post('".site_url('posts/remove')."', {id: post_id});
	});	
		
	$(document).delegate('.smile-vote-options a', 'click', function() {
		// send AJAX vote
		$.post('".site_url('posts/add_vote')."', 
			{id: $(this).parentsUntil('.stream_object').siblings('.post_id').html(), v:$(this).attr('data-type')});
			
		$(this).parentsUntil('.stream_object').children('.smile-vote').html($(this).html());
	});
	
	streamLoadUrl = '".$ajax_url."';
	
	run_tag_js('".site_url('posts/edit_tags')."');

</script>");?>