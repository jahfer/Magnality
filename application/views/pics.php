<div id="sub-container" class="clearfix">
	<h1 class="small-title" style="margin-top:20px">Pics</h1>
	<p class="small subtitle">Displaying all <?=count($pics)?> pictures</p>
	<ul id="notifications">
		<?=$this->media_model->show_media_grid($pics)?>
	</ul>
</div>