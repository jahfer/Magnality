<h1 class="small-title" style="margin-top:20px">Notifications</h1>
<p class="small subtitle">All of your notifications on this account</p>
<ul id="notifications">
	<?=$this->notification_model->show_list($query)?>
</ul>