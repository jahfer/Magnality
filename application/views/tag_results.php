	<?$is_logged_in = $this->session->userdata('is_logged_in');
	if(isset($is_logged_in) && $is_logged_in == true) : ?>			
		<div id="filter-bar" class="clearfix">
			<ul id="filters">
				<li><input type="button" value="Write" id="write-button" class="ir" /></li>
				<li <?=(isset($if_home)) ? "class='active'" : "";?>><?=anchor('home', "All")?></li>
				<?$this->membership_model->print_user_filters();?>
				<li class='active'><?=anchor('tags/'.$tag_url, "Tag: &#8220;$tag&#8221;");?></li>
			</ul>
			<ul id="filter-settings">		
				<input type="button" value="+ Add custom filter" id="add-button" class="ir" />
				or <?=anchor('filter/edit', 'edit filters &rarr;');?>
			</ul>
		</div>		
	<?endif?>

<div id="stream">
<?if($query->num_rows > 0) : ?>
	<p id="filter-desc">Showing all posts tagged: <?=$tag?></p>
<?endif?>
	<ul id="stream-list">	
		<?php					
			if($query->num_rows > 0)			
				$this->post_model->print_stream($query);
			else
				echo "<p class='error'>No results for <b>$tag</b> found.</p>";		
		?>		
	</ul>	
</div>