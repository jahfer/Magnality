<div id="sub-container"><p <?=$class?>><?=$status_msg?></p>

<h1 class="small-title" style="margin-top:20px">My Filters</h1>
<p class="small subtitle"> Please use only simple characters for filter names and tags (A-Z, 0-9)</p>
<div id="filter-list">
	<?
		if($filters) {
			
			echo form_open('filter/delete');
				foreach($filters->result() as $filter) {
					$tags = str_replace("+", " ", $filter->filter_string);
				
					echo 
					
					"<div class='filter' data-id='{$filter->id}'>
						".form_checkbox('filters[]', $filter->name, FALSE)." 
						<span class='editable name'>$filter->name</span>
						<span class='editable tags'>$tags</span>
					</div>";
				}			
				echo form_submit(array('name' => 'submit', 'value' => 'Delete Selected Filters', 'class' => 'submit btn'));
			echo form_close();
		} else
			echo 'No filters found. Try creating your own custom filter from the toolbar on the '.anchor('home', 'home page').'.';	
		
	?>
</div>

<?$this->javascript->addScript("
<script>
	$(document).delegate('.editable', 'click', function() {
	  $(this).removeClass('editable');
	  var value = $(this).html(),
	  		width = ($(this).width() > 100) ? $(this).width() : 100;
	  $(this).html('<input id=\"filter_edit\" style=\"width:'+width+'px\" value=\"'+value+'\"/>');
	  $('#filter_edit').focus();
	  var id = $(this).parent().attr('data-id')
	  		type = $(this).attr('class');
	  
	  $('#filter_edit').focusout(function() {
		  	$.ajax({
		  		url: '".site_url('filter/edit')."',
		  		type: 'POST',
		  		context: $(this).parent(),
		  		data: { 
		  			new_val: $(this).val(),
		  			filter_id: id,
		  			data_type: type 
		  		},
		  		success: function(msg) {
		  			$(this).html(\"<span class='editable'>\"+msg+\"</span>\");
		  		}
		  	});				
	  });
	  
	  
	  $('#filter_edit').bind('keypress', function(e) {
		  	var code = (e.keyCode ? e.keyCode : e.which);
		  	if(code == 13) {	
	  		  	$.ajax({
	  		  		url: '".site_url('filter/edit')."',
	  		  		type: 'POST',
	  		  		context: $(this).parent(),
	  		  		data: { 
	  		  			new_val: $(this).val(),
	  		  			filter_id: id,
	  		  			data_type: type 
	  		  		},
	  		  		success: function(msg) {
	  		  			$(this).html(\"<span class='editable'>\"+msg+\"</span>\");
	  		  		}
	  		  	});		
	  		  	return false;					
		  	};
	  });
	  
	});
</script>")?>
</div>