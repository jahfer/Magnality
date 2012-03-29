<?php

// open db
// select all "unmailed" notifications
// foreach loop
	// collect subscribers' emails
	// batch send email
// mark notifications as mailed
// close db

$to      = "";

$message = "";

$header = "MIME-Version: 1.0" . "\r\n";
$header .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
$header .= 'From: Magnality <do_not_reply@magnality.net>' . "\r\n";

$header .= 'Bcc: ';		
$header .= "\r\n";

mail($to, $subject, $message, $header);	