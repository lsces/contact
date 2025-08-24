<?php
//read the subjects of the demo mailbox

require_once 'Mbox.php';
require_once 'mimeDecode.php';

//reads a mbox file
$file = '/srv/website/bitweaver/contact/data/Stockport';
echo 'Using file ' . $file . "\n";

$mbox = new Mail_Mbox($file);
$mbox->open();

for ($n = 0; $n < $mbox->size(); $n++) {
    $message = $mbox->get($n);

    preg_match('/Subject: (.*)$/m', $message, $matches);
    $subject = $matches[1];
    echo 'Mail #' . $n . ': ' . $subject . "\n\n\n\n";
    $Decoder = new Mail_mimeDecode( $message );
    $params = array(
    'include_bodies' => TRUE,
    'decode_bodies'  => TRUE,
    'decode_headers' => TRUE
	);
	$Decoded = $Decoder->decode($params);   
	print_r($Decoded); 
}

$mbox->close();
?>