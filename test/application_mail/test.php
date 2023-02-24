<?php

$config = require __DIR__.'/config.php';

$mbox = imap_open($config['server_smtp_mail'], $config['id_mail'], $config['api_key_mail']);
 
$attachmentfolder = "./attachmentfolder/";

//echo "<h1>Mailboxes</h1>\n";
$folders = imap_listmailbox($mbox, "{imap.ionos.fr:993/imap/ssl/novalidate-cert}", "*");
$nbmail = imap_num_msg($mbox);



$list = imap_getmailboxes($mbox, "{imap.ionos.fr:993/imap/ssl/novalidate-cert}", "*");
if (is_array($list)) {
    foreach ($list as $key => $val) {
        echo "($key) ";
        echo imap_utf7_decode($val->name) . ",";
        echo "'" . $val->delimiter . "',";
        echo $val->attributes . "<br />\n";

        $pos = strpos($val->name, '}');
        $rest = substr($val->name, $pos+1);

        echo $rest;
        echo "\n";

        $utf7_folder_name = (str_contains($rest, "Objets"))?mb_convert_encoding("Objets envoyés", "UTF7-IMAP", "UTF-8"):mb_convert_encoding($rest, "UTF7-IMAP", "UTF-8");



        //$mboxbis = imap_reopen($mbox, $utf7_folder_name) or die(implode(", ", imap_errors()));
        $mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.$utf7_folder_name) or die(implode(", ", imap_errors()));

        $headers = imap_headers($mbox);

        if ($headers == false) {
           echo "Appel échoué<br />\n";
        } else {
           foreach ($headers as $val) {
              echo $val . "<br />\n";
           }
        }

    }
} else {
    echo "imap_getmailboxes a échoué : " . imap_last_error() . "\n";
}
 
//$spambox = imap_reopen($mbox, "{imap.ionos.fr:993/imap/ssl/novalidate-cert}Spam");
//$nbmailspam = imap_num_msg($spambox);

//var_dump( gettype($spambox) );
//var_dump($spambox);


$mbox = imap_open($config['server_smtp_mail'], $config['id_mail'], $config['api_key_mail']) or die(implode(", ", imap_errors()));

//$mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.imap_utf7_encode('Spam')) or die(implode(", ", imap_errors()));
//$mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.imap_utf7_encode('Brouillons')) or die(implode(", ", imap_errors()));
//$mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.imap_utf7_encode('Corbeille')) or die(implode(", ", imap_errors()));
//$mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.imap_utf7_encode('INBOX')) or die(implode(", ", imap_errors()));
$utf7_folder_name = mb_convert_encoding("Objets envoyés", "UTF7-IMAP", "UTF-8");
$mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.$utf7_folder_name) or die(implode(", ", imap_errors()));


echo "<h1>dossiers mails</h1>\n";
if ($folders == false) {
    echo "Appel échoué<br />\n";
} else {
    foreach ($folders as $val) {
        echo $val . "<br />\n";
    }
}

//echo imap_num_msg($mbox);
echo "<h1>en-têtes dans INBOX</h1>\n";
$headers = imap_headers($mbox);

if ($headers == false) {
    echo "Appel échoué<br />\n";
} else {
    foreach ($headers as $val) {
        echo $val . "<br />\n";
    }
}

imap_close($mbox);

?>




