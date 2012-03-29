
<?$this->load->view('filter_panel');?>
	
	<footer id="page-bottom">
		<p style="float:left">Magnality &copy; 2011</p>
		<!--<p style="float:right">About &middot; Privacy Policy &middot; Terms of Service &middot; Settings</p>-->
	</footer>
	
</div><!-- end of #container -->
	
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script>!window.jQuery && document.write(unescape('%3Cscript src="js/libs/jquery-1.5.1.js"%3E%3C/script%3E'))</script>
<script src="<?=base_url()?>js/mylibs/jquery-ui-1.8.16.custom.min.js"></script>

<script src="<?=base_url()?>js/script.js"></script>
<script src="<?=base_url()?>js/mylibs/jquery.elastic.min.js" async></script>
	
<?php 
	$this->javascript->addScript("	
		$('#mark_notifications').click(function(e) {
			$.post('".site_url('notifications/mark_read')."');
			$('#notifications').html('No new notifications.');
			$('#notification-count')
			.removeClass().addClass('notif_0');
			$('#count').html('0');
			e.preventDefault();
		});
		 var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
		 (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;
		 g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
		 s.parentNode.insertBefore(g,s)}(document,'script'));
	");
?>

<?=$this->javascript->output();?>

</body>
</html>