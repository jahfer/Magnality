<li class='stream_object clearfix'>		
	
	<span class='post_id'>{id}</span>
	<div class='timestamp'>
		<span title='{timestamp}'>{time}</span>
		<a href='javascript:void(0)' class='smile-vote'><img src='{smile_url}' /></a>
		<div class='smile-vote-container'>
			<img src='{smile_vote_panel}' class='smile-vote-panel' />
			<div class='smile-vote-options'>
				<a href='javascript:void(0)' data-type='1'><img src='{smile_happy}' /></a>
				<a href='javascript:void(0)' data-type='2'><img src='{smile_shock}' /></a>
				<a href='javascript:void(0)' data-type='3'><img src='{smile_sad}' /></a>
			</div>
		</div>
		<!--<a href='{permalink}'>{reply_count}</a>-->
	</div>
	<div class='user_pic'>
		<a href='{user_id}'><img src='{display_pic}' width="40" height="40" /></a>				
		<span class='tag emotion'>{emotion}</span>		
	</div>
	<div class="post_main">
		<p class='meta'>
			<a href='{user_id}' class='name'>{full_name}</a>
		</p>
		<p class='text'>{post_text}</p>
		{media_if_available}
		<div class="tag_container">{tags}<input type='button' value='+ edit' class='tag edit' /></div>
		{comment_form_if_available}
		{comments_if_available}
	</div>
	{remove_link_if_available}
</li>