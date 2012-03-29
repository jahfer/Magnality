<!doctype html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title>
	<? 		
		$p = $this->uri->segment(1);
		switch ($p) {
			case "home":
			case "":
				echo "My Mag";
				break;
			case "filter":
				echo str_replace("-", " ", $this->uri->segment(3))." | Filters";
				break;			
			case "users":
				echo "Profiles | Magnality";
				break;
			case "edit-filters":
				echo "My Filters | Magnality";
				break;
			case "posts":
				echo "Comments | Magnality";
				break;
			case "tags":
				echo "Magnality / Tags - ".$this->uri->segment(2);
				break;
			case "search":
				echo "Magnality / Search - ".$this->uri->segment(2);
				break;
			default:
				echo "Magnality";
		}
	?>
	</title>
	<meta name="description" content="">
	<meta name="author" content="">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" href="<?=base_url()?>favicon.ico">
	<link rel="apple-touch-icon" href="<?=base_url()?>apple-touch-icon.png">
	
	
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="<?=base_url()?>css/style.css" type="text/css" media="screen" />
	<link type="text/plain" rel="author" href="<?=base_url()?>humans.txt" />
	
	<script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js"></script>
	
</head>
<body class="clearfix">

	<header id="page-top">
	
		<div class="container clearfix">
		<a href="<?=base_url()?>"><h1 id="logo" class="ir">Magnality</h1></a>
		<?php 
		
			$is_logged_in = $this->session->userdata('is_logged_in');
			if/* user not logged in */(!isset($is_logged_in) || $is_logged_in != true) :			
				$this->load->view("login_form"); 
				
			else/* if user is logged in */ : 
				$atts = array(
			     'width'      => '500',
			     'height'     => '400',
			     'resizable'  => 'yes',
			   ); ?>			
				<div id='settings-dropdown'>
					<a id='dropdown-link' href='javascript:void(0)' ><?=$LOGGED_REAL_NAME?><img class="arrow" src="<?=base_url();?>css/img/down_arrow.png" width=5 height=8 /></a>
					<ul id='dropdown-panel'>
						<li class="disabled"><?=$LOGGED_EMAIL?></li> | 
						<?=anchor('users/'.$LOGGED_USER_ID, '<li>Profile</li>');?> |
						<?=anchor('edit-filters', '<li>My Filters</li>');?> | 
						<?=anchor_popup('chatroom', '<li>Chat <span class=small style=color:#aaa>(beta)</span></li>', $atts);?> | 
						<?=anchor('login/logout', '<li>Log out</li>');?>
					</ul>
				</div>
				<?$count = $this->notification_model->get_unread_count(); ?>
				<div id='notification-count' class="notif_<?=$count?>">
					<span id="count"><?=$count?></span>
					<div id="notif-dropdown">
						<?=$this->notification_model->load_notifications();?>
					</div>
				</div>
			<?	
		      
		endif;?>
		</div>
	</header>
		<div class="container">