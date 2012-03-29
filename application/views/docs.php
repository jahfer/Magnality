<div id="sub-container" class="clearfix">
	<h1 class="small-title" style="margin-top:20px">Documents</h1>
	<p class="small subtitle">Displaying all <?=count($docs)?> documents</p>
	<ul id="notifications">
		<?=$this->media_model->show_media_grid($docs)?>
	</ul>
</div>