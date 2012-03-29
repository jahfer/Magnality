<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8>
		<title>Tour: My Mag</title>
		
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		<link rel="shortcut icon" href="<?=base_url()?>favicon.ico">
		<link rel="apple-touch-icon" href="<?=base_url()?>apple-touch-icon.png">
		
		<link rel=stylesheet href="<?=base_url()?>css/tour.min.css" />
		
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold' rel='stylesheet' type='text/css'>
		<link type="text/plain" rel="author" href="<?=base_url()?>humans.txt" />
	</head>
	<body>
		<header>
			<div class="container">
				<?=anchor("/", "Go to My Mag &raquo;", "class='btn primary'");?>
			</div>
		</header>
		
		<div class="container">
			<img src="<?=$image?>" alt="" />
		</div>
	</body>
</html>