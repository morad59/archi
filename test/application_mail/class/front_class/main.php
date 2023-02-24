<?php
header('Content-Type: text/html; charset=utf-8');

/*
$hostname = '{imap.ionos.fr:993/imap/ssl}INBOX';
$username = 'contact@appliops.com'; 
$password = 'Encule1985&&a&'; 
*/
$hostname = '{smtp.exchange2019.ionos.fr:993/imap/ssl}INBOX';
$username = 'contact@appliops.eu';
$password = 'W123456w&?';
$searchArray = array('SUBJECT'=>'test', 'SINCE'=>date('j F Y',strtotime('1 month ago')));

$saveToPath = 'dump/'; //change this

$unzipDest = 'files/'; //change this

require "exattach.class.php"; //may need to change this

$xa = new exAttach($hostname,$username,$password);
$xa->get_files($searchArray, $saveToPath);
$xa->extract_zip_to($unzipDest);
?>
