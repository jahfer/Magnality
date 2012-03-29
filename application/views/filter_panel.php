<div id="filter_panel" class="panel">			
	<?php
		echo form_open('filter/add');		
		echo '<h1 class="small-title">Add Custom Filter</h1>';
		echo '<p class="small">Title (A-Z, 0-9 and spaces)</p>';
		echo form_input(array('name' => 'title', 'id' => 'new-filter-title'));
		echo '<br>';
		echo '<p class="small">Tags to include (e.g. "art design photography painting")</p>';
		echo form_textarea(array('name' => 'tags', 'id' => 'new-filter-tags', 'rows'=>3));
		echo '<br>';
		echo form_hidden('uri', $this->uri->uri_string());
		echo form_submit(array('name' => 'submit', 'value' => 'Save', 'class' => 'btn primary'));
		echo form_close();
	?>		
</div>