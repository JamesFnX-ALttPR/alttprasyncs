<?php
$to = "alttprasyncs@gmail.com";
$subject = "PHP Test Email";
$txt = "This is a test message." . "\r\n" . "You may disregard this test.";
$headers = "From: alttprasyncs@gmail.com" . "\r\n" . "CC: jamesfnx@gmail.com";

mail($to, $subject, $txt, $headers);
?>
