<div id="login_form">
	<?php
		
		echo form_open('login/validate');
		
		if(isset($login_fail)) echo '<p class="error">Email or password incorrect.</p>';
		
		echo "<div class='form-label-block'>";		
		echo form_label("Email", "login-email");
		echo form_input(array('name' => 'email', 'id' => 'login-email', 'class'=>'glow', 'value' => set_value("email")));
		echo "</div><div class='form-label-block'>";
		echo form_label("Password", "login-password");
		echo form_password(array('name' => 'password', 'id' => 'login-password', 'class'=>'glow', 'value' => set_value("password")));
		echo '</div>';
		if(isset($uri_redirect))
			echo form_hidden('uri-redirect', $uri_redirect);
		echo form_submit(array('name' => 'submit', 'value' => 'Log in', 'class' => 'primary btn'));
		echo form_close();
	
	?>
</div>