<div id="sub-container" class="clearfix">
	<div id="about" class="clearfix">
		<img id="site-desc" src="<?=base_url()?>img/welcome.gif" />		
		
		<?php	
			echo form_open('login/create_member', array('id'=>'signup'));
			
			echo form_error('first_name', '<p class="error">', '</p>');
			echo form_label("First Name", "signup-firstname");
				echo form_input(array('name'=>'first_name', 'id' => 'signup-firstname', 'class'=>'glow', 'value' => set_value('first_name')));
				
			echo form_error('last_name', '<p class="error">', '</p>');
			echo form_label("Last Name", "signup-lastname");
				echo form_input(array('name'=>'last_name', 'id' => 'signup-lastname', 'class'=>'glow', 'value' => set_value('last_name')));
				
			echo form_error('email', '<p class="error">', '</p>');
			echo form_label("Email", "signup-email");
				echo form_input(array('name'=>'email', 'id' => 'signup-email', 'class'=>'glow', 'value' => set_value('email')));
				
			echo form_error('password', '<p class="error">', '</p>');
			echo form_label("Password", "signup-password");
				echo form_password(array('name'=>'password', 'id' => 'signup-password', 'class'=>'glow', 'value' => set_value('password')));
				
			echo form_error('password2', '<p class="error">', '</p>');
			echo form_label("Confirm Password", "signup-password2");
				echo form_password(array('name'=>'password2', 'id' => 'signup-password2', 'class'=>'glow', 'value' => set_value('password2')));
			
			echo form_submit(array('name' => 'submit', 'value' => 'Join Magnality', 'class' => 'primary btn'));
			echo form_close();
		?>
	</div>
</div>