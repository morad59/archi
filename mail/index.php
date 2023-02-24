<?php

$config = require __DIR__.'/config_mail.php';

$htmlmsg = "";
$plainmsg = "";
$charset = "";
$attachments = array();
$attachments2 = array();

if ( isset($_POST["username"]) && $_POST["username"] != "" &&  isset($_POST["password"]) &&  $_POST["password"] != "" && !isset($_POST["read"]) ){

   $mbox = imap_open($config['server_smtp_mail'], base64_decode($_POST["username"]), base64_decode($_POST["password"]) );

   $folders = imap_listmailbox($mbox, "{imap.ionos.fr:993/imap/ssl/novalidate-cert}", "*");
   $nbmail = imap_num_msg($mbox);

   $list = imap_getmailboxes($mbox, "{imap.ionos.fr:993/imap/ssl/novalidate-cert}", "*");

   $mail_box_archi = array('indice' => null, 'title' => null, 'content' => null); 

   if (is_array($list)) {
     $content =array();
     $indice =array();
     $title = array();
     $headers = array();
     foreach ($list as $key => $val) {

        $indice = $key; 

        $pos = strpos($val->name, '}');
        $rest = substr($val->name, $pos+1);

        $utf7_folder_name = (stripos($rest, "Objets")!== false)?mb_convert_encoding("Objets envoyés", "UTF7-IMAP", "UTF-8"):mb_convert_encoding($rest, "UTF7-IMAP", "UTF-8");

        $mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.$utf7_folder_name) or die(implode(", ", imap_errors()));
        $nbmail_boxbis = imap_num_msg($mbox);

        array_push($title,array("label_mailbox"=>$utf7_folder_name, "nbmail_mailbox"=>$nbmail_boxbis ));

        for ($i=1; $i<=$nbmail_boxbis; $i++) {
          $header = imap_header($mbox, $i);
          $body = imap_body($mbox, $i);
          $header_date = $header->date;
          $header_subject = $header->subject;
          $header_from = $header->from[0]->personal;
          $header_reply_to = imap_8bit(trim($header->reply_to[0]->mailbox.'@'.$header->reply_to[0]->host));
          $header_overview = imap_fetch_overview($mbox,$i,0);
          $indice_titlebox = array("indice_mailbox"=>$indice,"titre_mailbox"=>$rest,"indice_mail"=>$i);
          array_push($content,array($i, $indice_titlebox, $body, $header_date, $header_subject, $header_from, $header_reply_to, $header_overview));
          array_push($headers, array($indice_titlebox, imap_headerinfo($mbox,$i)) );
        }


/*
        $headers = imap_headers($mbox);

        if ($headers == false) {
              array_push($content,"pas trouvé".$indice.$rest." ".$title.$nbmail_boxbis."\n" );
        } else {
           foreach ($headers as $val) {
              array_push($content,$indice.$rest.$val.$nbmail_boxbis.$header );
           }
        }
*/

     //$mail_box_archi['content'] = $content; 
     $mail_box_archi['indice'] = $indice; 
     $mail_box_archi['title'] = $title; 
     $mail_box_archi['nbmail'] = $nbmail; 
     $mail_box_archi['headers'] = $headers; 

     }
     imap_close($mbox);
     echo json_encode($mail_box_archi);
   }else{
     $arr = array('a' => base64_decode($_POST["username"]), 'b' => base64_decode($_POST["password"]), 'c' => $config['server_smtp_mail'], 'd' => $mbox );
     echo json_encode($arr);
   }
}
else{
   if ( isset($_POST["read"]) && $_POST["read"] != "" &&  isset($_POST["username"]) &&  $_POST["username"] != "" &&  isset($_POST["password"]) &&  $_POST["password"] != ""  ){
      $mbox = imap_open($config['server_smtp_mail'], base64_decode($_POST["username"]), base64_decode($_POST["password"]) );
      $folders = imap_listmailbox($mbox, "{imap.ionos.fr:993/imap/ssl/novalidate-cert}", "*");
      $nbmail = imap_num_msg($mbox);

      $list = imap_getmailboxes($mbox, "{imap.ionos.fr:993/imap/ssl/novalidate-cert}", "*");

      $mail_box_archi = array('indice' => null, 'title' => null, 'content' => null);

      if (is_array($list)) {
         $content =array();
         $indice =array();
         $title = array();
         $headers = array();
         foreach ($list as $key => $val) {

           if ( $key == $_POST["content_header"][0]["indice_mailbox"]    ) {
               $indice = $key;

               $pos = strpos($val->name, '}');
               $rest = substr($val->name, $pos+1);

               $utf7_folder_name = (stripos($rest, "Objets")!== false)?mb_convert_encoding("Objets envoyés", "UTF7-IMAP", "UTF-8"):mb_convert_encoding($rest, "UTF7-IMAP", "UTF-8");

               $mboxbis = imap_reopen($mbox, '{imap.ionos.fr:993/imap/ssl}'.$utf7_folder_name) or die(implode(", ", imap_errors()));
               $nbmail_boxbis = imap_num_msg($mbox);

               array_push($title,array("label_mailbox"=>$utf7_folder_name, "nbmail_mailbox"=>$nbmail_boxbis ));
               //getmsg($mbox,imap_uid($mbox,$i));
               //getmsg($mbox,imap_uid($mbox,$indice));
               //getmsg($mbox,$indice);

               for ($i=1; $i<=$nbmail_boxbis; $i++) {
                  if ( $i == (($_POST["content_header"][0]["indice_mail"])-0)    ) {
                     $header = imap_header($mbox, $i);
                     $body = imap_body($mbox, $i);
                     $header_date = $header->date;
                     $header_subject = $header->subject;
                     $header_from = $header->from[0]->personal;
                     $header_reply_to = imap_8bit(trim($header->reply_to[0]->mailbox.'@'.$header->reply_to[0]->host));
                     $header_overview = imap_fetch_overview($mbox,$i,0);
                     $indice_titlebox = array("indice_mailbox"=>$indice,"titre_mailbox"=>$rest,"indice_mail"=>$i);
                     //getmsg($mbox,imap_uid($mbox,$i));
                     //$structure = imap_fetchstructure($mbox,$i);
                     /* 
                     if (!$structure->parts){  // simple
                        getpart($mbox,$i,$structure,0); 
                     }else {  
                            
                            foreach ($structure->parts as $partno0=>$p){  
                              getmsg($mbox,imap_uid($mbox,$i));
                              //getpart($mbox,imap_uid($mbox,$i),$p,$partno0+1);
                            }
                     }
                     */ 

                     array_push($content,array($i, $indice_titlebox, $body, $header_date, $header_subject, $header_from, $header_reply_to, $header_overview,$header));
                     //array_push($headers, array($indice_titlebox, imap_headerinfo($mbox,$i), $structure ) );
                     //array_push($headers, array($indice_titlebox, imap_headerinfo($mbox,$i), $htmlmsg,$plainmsg,$charset,$attachments ) );
                     //array_push($headers, array($indice_titlebox, imap_headerinfo($mbox,$i), $charset ) );
                     //array_push($headers, array($indice_titlebox, imap_headerinfo($mbox,$i), $attachments ) );
                     //array_push($headers, array($indice_titlebox, imap_headerinfo($mbox,$i), $htmlmsg ) );
                     //array_push($headers, array($indice_titlebox, imap_headerinfo($mbox,$i), $plainmsg ) );
                  }
               }
               $mail_box_archi['content'] = $content;
               $mail_box_archi['indice'] = $indice;
               $mail_box_archi['title'] = $title;
               $mail_box_archi['nbmail'] = $nbmail;
               $mail_box_archi['headers'] = $headers;
           }
         }
         imap_close($mbox);
header('Content-type: image/jpeg');
         echo json_encode($mail_box_archi);
         //echo json_encode($_POST["content_header"][0]["indice_mailbox"]);
         //echo json_encode( array('a' => 51, 'b' => 52) );
      }
      //echo json_encode( array('a' => 51) );
   }
   //$arr = array('a' => 51, 'b' => 52, 'c' => 53, 'd' => 54, 'e' => 55);
   //echo json_encode($arr);
}
/*
function getpart($mbox,$mid,$p,$partno) {
    // $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple
    global $htmlmsg,$plainmsg,$charset,$attachments;

    // DECODE DATA
    $data = ($partno)?
        imap_fetchbody($mbox,$mid,$partno):  // multipart
        imap_body($mbox,$mid);  // simple
    // Any part may be encoded, even plain text messages, so check everything.
    if ($p->encoding==4)
        $data = quoted_printable_decode($data);
    elseif ($p->encoding==3)
        $data = base64_decode($data);

    // PARAMETERS
    // get all parameters, like charset, filenames of attachments, etc.
    $params = array();
    if ($p->parameters)
        foreach ($p->parameters as $x)
            $params[strtolower($x->attribute)] = $x->value;
    if ($p->dparameters)
        foreach ($p->dparameters as $x)
            $params[strtolower($x->attribute)] = $x->value;

    // ATTACHMENT
    // Any part with a filename is an attachment,
    // so an attached text file (type 0) is not mistaken as the message.
    if ($params['filename'] || $params['name']) {
        // filename may be given as 'Filename' or 'Name' or both
        $filename = ($params['filename'])? $params['filename'] : $params['name'];
        // filename may be encoded, so see imap_mime_header_decode()
        $attachments[$filename] = $data;  // this is a problem if two files have same name
    }

    // TEXT
    if ($p->type==0 && $data) {
        // Messages may be split in different parts because of inline attachments,
        // so append parts together with blank row.
        if (strtolower($p->subtype)=='plain')
            $plainmsg.= trim($data) ."\n\n";
        else
            $htmlmsg.= $data ."<br><br>";
        $charset = $params['charset'];  // assume all parts are same charset
    }

    // EMBEDDED MESSAGE
    // Many bounce notifications embed the original message as type 2,
    // but AOL uses type 1 (multipart), which is not handled here.
    // There are no PHP functions to parse embedded messages,
    // so this just appends the raw source to the main message.
    elseif ($p->type==2 && $data) {
        $plainmsg.= $data."\n\n";
    }

    // SUBPART RECURSION
    if ($p->parts) {
        foreach ($p->parts as $partno0=>$p2)
            getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
    }
}
*/
function getmsg($mbox,$mid) {
    // input $mbox = IMAP stream, $mid = message id
    // output all the following:
    global $charset,$htmlmsg,$plainmsg,$attachments;
    $htmlmsg = $plainmsg = $charset = '';
    $attachments = array();

    // HEADER
    $h = imap_header($mbox,$mid);
    // add code here to get date, from, to, cc, subject...

    // BODY
    $s = imap_fetchstructure($mbox,$mid);
    if (!$s->parts)  // simple
        getpart($mbox,$mid,$s,0);  // pass 0 as part-number
    else {  // multipart: cycle through each part
        foreach ($s->parts as $partno0=>$p)
            getpart($mbox,$mid,$p,$partno0+1);
    }
}

function getpart($mbox,$mid,$p,$partno) {
    global $htmlmsg,$plainmsg,$charset,$attachments, $attachments2;

    $folder = "attachment";
    if(!is_dir($folder))
    {
       mkdir($folder);
    }

    $data = ($partno)?imap_fetchbody($mbox,$mid,$partno):imap_body($mbox,$mid); 
    if ($p->encoding==4){ 
        $data = quoted_printable_decode($data);
    }
    elseif ($p->encoding==3){ 
        $data = base64_decode($data);
    }
 

    $params = array();
    if ($p->ifparameters){  
        foreach ($p->parameters as $x){ 
            if(strtolower($x->attribute) == 'name'){
              $params[strtolower($x->attribute)] = $x->value;
            }
        }
        $filename = ($params['filename'])? $params['filename'] : $params['name'];
        $attachments[$filename] = $data;  
        $fp = fopen("./". $folder ."/". $mid . "-" . $filename, "w+");
        fwrite($fp, $attachment[$filename]); 
        fclose($fp);
    }

    if ($p->ifdparameters){ 
        foreach ($p->dparameters as $x){ 
            if(strtolower($x->attribute) == 'filename'){
              $params[strtolower($x->attribute)] = $x->value;
            }
        }
        $filename = ($params['filename'])? $params['filename'] : $params['name'];
        $attachments[$filename] = $data;  
        $fp = fopen("./". $folder ."/". $mid . "-" . $filename, "w+");
        fwrite($fp, $attachment[$filename]); 
        fclose($fp);



    }

    if ($params['filename'] || $params['name']) {
        $filename = ($params['filename'])? $params['filename'] : $params['name'];
        $attachments[$filename] = $data;  
      }
    


    if ($p->type==0 && $data) {

        if (strtolower($p->subtype)=='plain'){  
            $plainmsg.= trim($data) ."\n\n";
        }else{   
            $htmlmsg.= $data ."<br><br>";
        }
        $charset = $params['charset'];  
        if(strtolower($x->attribute) == 'charset'){
            $params[strtolower($x->attribute)] = $x->value;
        }
    }
    elseif ($p->type==2 && $data) {
        $plainmsg.= $data."\n\n";
    }
if ($p->parts) {
        foreach ($p->parts as $partno0=>$p2)
           getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
   }
//array_push($attachments2, $params);   
}
?>




