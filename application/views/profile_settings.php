<p <?=$class?>><?=$status_msg?></p>

<h1 class="small-title" style="margin-top:20px">Profile Settings</h1>
<p class="small subtitle">Adjust common settings for your user profile</p>

<img id='profile-img' src="<?=$display_picture_url?>" />

<div id="edit-profile">
	<?php		
		// open form
		echo form_open_multipart('userinfo/update_profile');
		
		// display picture
		echo form_label("Change Picture", "edit-pic", array('class'=>'first'));
		echo form_upload('userfile', 'id=edit-pic');
		
		$selected = ($profile->title_num) ? $profile->title_num : 7;
		
		// title
		$position = array(
		'Student' => array(
			'1' => 'First Year', 
			'2' => 'Second Year', 
			'3' => 'Third Year', 
			'4' => 'Fourth Year', 
		),
		'Staff' => array( 
			'5' => 'Instructor', 
			'6' => 'Teaching Assistant', 
		),
		'7' => 'Other');			
		echo form_label("Title", "edit-title");
		echo form_dropdown('title', $position, $selected, 'id=edit-title');
		
		// about me
		echo form_label("About Me", "edit-about");
		$data = array(
		'name' => 'about',
		'id' => 'edit-about',
		'value' => $profile->about
		);
		echo form_textarea($data);
		
		// email
		echo form_label("Email", "edit-email");
		echo form_input('email', $profile->email, "id='edit-email'");
		
		// website
		echo form_label("Website", "edit-website");
		echo form_input('website', $profile->website, "id='edit-website'");
		
		// submit
		echo "<br>".form_submit('upload', 'Save Changes', 'class="primary btn"');
		echo form_close();
	?>
</div>