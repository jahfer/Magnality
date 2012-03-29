{posts}
<a class="header" href="#"><div class="discussion_obj"><p class="discussion_text"><strong>{author}</strong>&nbsp;&nbsp;{text}</p><time pubdate class="post_time">{timestamp}</time> <p class="comm_count">{reply_count}</p></div></a>
<div class="discussion_comments">
	<a href="{base_url}posts/{id}" class="permalink">Go to This Post &raquo;</a>
	{image_if_available}
	<div class="tag_container">{tags}</div>
	<ul class="inline-comments" class="clearfix">
		{comments}
	</ul>
	
	<form action="{comment_url}" method="post" accept-charset="utf-8" class="comment_form">
		<textarea name="text" cols="90" rows="3" class="comment_text" placeholder="Add a comment…"></textarea><br>
		<input type="submit" name="submit" value="Reply" class="btn reply-btn"  />
		<input type="hidden" name="uri_redirect" value="/" />	
		<input type="hidden" name="post_id" value="{id}" />	
		<input type="hidden" name="parent" value="0" />
	</form>
	
</div>
{/posts}