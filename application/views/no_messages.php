<? 
	$data['LOGGED_USER_ID'] = $this->session->userdata('user_id');	
	$data['LOGGED_REAL_NAME'] = $this->membership_model->fetch_real_name($data['LOGGED_USER_ID']);
	$data['LOGGED_EMAIL'] = $this->membership_model->fetch_email($data['LOGGED_USER_ID']);
?>

<? $this->load->view('inc/header', $data) ?>	
</div>
<div id="filter-bar" class="clearfix">
		<ul id="filters" class="container">
			<li class='active' id="today"><a href='#'>My Mag</a></li>
				<?
					$this->filter_model->print_user_filters();
				?>		
			<li id="add-filter" ><a href='javascript:void(0)' title='Add filter' id="add-button">+ Add Filter</a></li>
		</ul>
</div>

<div class="container" id="daily_mag">

	<div id="daily_mag_main">
		<? $this->load->view('write_panel.php') ?>
		
		<div class="mag_block" style="text-align:center; margin: 40px 0 100px;">
			<img src="<?=base_url()?>img/no_messages.png" alt="No messages" />
		</div>
		
	</div>
	
	<div id="sidebar">	
		<?=$this->sidebar_model->load_search(); ?>	
				
		<div class="sidebar-object clearfix">
			<h1 class="small-title">Notifications</h1>
			<ul id="notifications">
				{notifications}
			</ul>
		</div>		
		
		<?
		$mag_filters = $this->filter_model->fetch_user_filters(0);				
		$filter_list = "";
		$count = count($mag_filters->result());
		
		$random_mag_filters = $mag_filters->result();
		shuffle($random_mag_filters);
		
		$i = 1;
		
		foreach($random_mag_filters as $filter) {		
			if($this->filter_model->_check_if_subscribed($filter->user_id, $filter->name) == TRUE)
				continue;
			
			$formatted_name = str_replace(" ", "-", $filter->name);			
						
			$filter_list .= "<li>".anchor("filter/0/$formatted_name", $filter->name);
			
			$filter_list .= anchor('#', 'Subscribe',
				'class = "btn btn-right subscribe" data-o='.base64_encode($filter->user_id).' data-f='.base64_encode($formatted_name)
			);
				
			$filter_list .= "</li>";
			if($i++ == 3) break;
		}
		
		if($filter_list != "")
			echo "<h1 class=small-title>Suggested Filters</h1><ul id='mag_filters'>".$filter_list;
		
		$this->javascript->addScript("
			<script>								
				$('.subscribe').click(function(e){
					sub('".site_url('filter/subscribe')."', $(this), 'sub', $(this).attr('data-o'), $(this).attr('data-f'));
					e.preventDefault();
				});
				
				$('.unsubscribe').click(function(e){
					sub('".site_url('filter/subscribe')."', $(this), 'unsub', $(this).attr('data-o'), $(this).attr('data-f'));
					e.preventDefault();
				});
			</script>
		");
		?>
			
	</div>

<? $this->javascript->addScript("	

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

</script>");?>


<?$this->load->view('inc/footer');?>