<?php

session_start([
    'cookie_lifetime' => 86400,
]);

/*gestion sessions ok*/
/*gestion dbSessions ok*/
/*gestion configIdPwSessions db1 ok hashTable2*/
/*gestion languages ok*/

/*
define('CLASS_DIR', 'class/front_class');
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);
spl_autoload_extensions('.class.php');
spl_autoload_register();
*/

require 'vendor/autoload.php';
$f3 = \Base::instance();

$languages=\ISO::instance()->languages();

$geo = \Web\Geo::instance();
$global_lang = strtolower($geo->location()["country_code"]);
$f3->set('lang',$global_lang );

$weather_key = '61ae8e8fd8d0f8f956a9600fff9b808f';
//echo( file_get_contents( 'http://api.openweathermap.org/data/2.5/weather?q='.strtolower($geo->location()["city"]).'&appid='.$weather_key));
/*********************************************************SESSION HANDLER**********************************************************/
$config = require __DIR__.'/config.php';

$pw_db = $config['pw_db'];
$id_db = $config['id_db'];
$na_db = $config['na_db'];
/*
$htmlmsg = "";
$plainmsg = "";
$charset = "";
$attachments = array();
*/
class obj implements Serializable {
    private $data;
    public function __construct($datas) {
       $this->data = $datas;
    }
    public function serialize() {
       return serialize($this->data);
    }
    public function unserialize($data) {
       $this->data = unserialize($data);
    }
    public function getData() {
       return (is_object($this->data))?$this->getData():$this->data;
     }
}

function translate($term){
        global $id_db, $pw_db, $na_db, $global_lang;
        $db = new mysqli("localhost", $id_db, $pw_db, $na_db);
        $rows=$db->query('SELECT `'.$global_lang.'` FROM `translate` WHERE `id` = '.$term );
        //$row = $rows->fetch_assoc();
        //return $row[$global_lang];
        return ;
}

function getUserIpAddr(){
   if(!empty($_SERVER['HTTP_CLIENT_IP'])){
       $ip = $_SERVER['HTTP_CLIENT_IP'];
   }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   }else{
       $ip = $_SERVER['REMOTE_ADDR'];
   }
   return $ip;
}
$db = new mysqli("localhost", $id_db, $pw_db, $na_db);
$rows=$db->query("SELECT * FROM `sessions` WHERE `data` != '' AND `ip` LIKE '".getUserIpAddr()."' ORDER BY `stamp` DESC");
$row = $rows->fetch_assoc();

if ( isset($row) ){

    global $id_db, $pw_db, $na_db;
    $db = new mysqli("localhost", $id_db, $pw_db, $na_db);
    $id_session = session_id();
 
    //charger les datas de la session sauvegarde
    $f3->set('SESSION.visitor',base64_encode( time() ) );
    $f3->set('SESSION.csrf',substr($row["data"], strrpos($row["data"], ":")+3, -2) );
    $newobj = new obj($_SESSION["payload"]);
    $newobj = unserialize($newobj->getData());
    if ($newobj == false || $newobj == NULL){
       $newobj = unserialize($row["data"]);
    }
    $_SESSION["id_utilisateur"] = $newobj->getData()["id_utilisateur"];
    $_SESSION["ville"] = $newobj->getData()["ville"];

    $f3->set('id_utilisateur',$_SESSION["id_utilisateur"]);
    $f3->set('ville',$_SESSION["ville"]);

    if ($newobj == false){
       $duree = time()+3600*24*7;
       $options = ["expires" => $duree, "path" => "/appliops_user_cookies/", "domain" => "appliops.com", "secure" => "TRUE", "httponly" => "TRUE", "samesite" => "Strict"];
       $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
       //$_SESSION["sss"] = (setcookie("appliops_user_cookie","a_u_c", $duree, "/appliops_user_cookies/", $domain, TRUE, TRUE))?$ser:"aucune datas";
       $_SESSION["visitor"] = base64_encode( $id_session );
 
       $myCsrf = bin2hex(openssl_random_pseudo_bytes(24));
       $id_session = session_id();
       $sess_datas["csrf"] = $myCsrf;
       $sess_datas["logged"] = false;
       $sess_datas["user_agent"] = $_SERVER['HTTP_USER_AGENT'];
       $sess_datas["id_ip_user_agent"] = base64_encode($_SERVER['HTTP_USER_AGENT']);
       $sess_datas["id_session"] = $id_session;
       $obj = new obj($sess_datas);
       $ser = serialize($obj);
       $_SESSION["payload"] = $ser;
       $newobj = new obj($datas_received_process);
       $ser = serialize($obj);
     
       if ( $datas_received_process["user_agent"] != $_SERVER['HTTP_USER_AGENT'] ){
 $rows=$db->query("INSERT INTO `sessions` (`session_id`,`data`,`ip`,`agent`,`stamp`) VALUES ('".$id_session."','".$ser."','".getUserIpAddr()."','".$_SERVER['HTTP_USER_AGENT']."','".time()."');");
       }
    }
/*
    $files_cookies = scandir('./appliops_user_cookies/');
    var_dump($files_cookies);
    for ($i = 1; $i < count($files_cookies); $i++) {
      $pos = strpos($files_cookies[$i], $_SESSION["csrf"]);
    }
*/
    $cookie_file_save = $id_session;

    if ( $datas_received = @file_get_contents("./appliops_user_cookies/".$cookie_file_save.".txt") ){
         
    }

    $datas_received_process = unserialize($datas_received);
    $drp = $datas_received_process;
    if ($_SESSION["csrf"]){

    }else{
       $myCsrf = bin2hex(openssl_random_pseudo_bytes(24));
       $drp["csrf"] = $_SESSION["csrf"];
       $_SESSION["csrf"] = $myCsrf;;
    }

    $obj = new obj($datas_received_process);
    $ser = serialize($obj);
    if ( gettype($datas_received_process) == "object" ){
        if ( $datas_received_process->getData()['user_agent'] != $_SERVER['HTTP_USER_AGENT'] ){
  $rows=$db->query("INSERT INTO `sessions` (`session_id`,`data`,`ip`,`agent`,`stamp`) VALUES ('".$id_session."','".$ser."','".getUserIpAddr()."','".$_SERVER['HTTP_USER_AGENT']."','".time()."');");
        }
    }else{
        if ( $datas_received_process["user_agent"] != $_SERVER['HTTP_USER_AGENT'] ){
  $rows=$db->query("INSERT INTO `sessions` (`session_id`,`data`,`ip`,`agent`,`stamp`) VALUES ('".$id_session."','".$ser."','".getUserIpAddr()."','".$_SERVER['HTTP_USER_AGENT']."','".time()."');");
        }
    }
 
    $cookie_file_save = substr( $_SESSION["csrf"], 0, strpos($_SESSION["csrf"], "\";") );
    $cookie_file_save = substr( $_SESSION["csrf"], 0, 32 );
    $cookie_file_save = $cookie_file_save."_".$newobj->getData()["id_ip_user_agent"];
    file_put_contents("./appliops_user_cookies/".$cookie_file_save.".txt", $ser );


}else{
    //ini_set('session.use_strict_mode', 1);
    session_start();
    global $id_db, $pw_db, $na_db;
    $db = new mysqli("localhost", $id_db, $pw_db, $na_db);
    $myCsrf = bin2hex(openssl_random_pseudo_bytes(24));
    $id_session = session_id();
    $sess_datas["csrf"] = $myCsrf;
    $sess_datas["logged"] = false;
    $sess_datas["user_agent"] = $_SERVER['HTTP_USER_AGENT'];
    $sess_datas["id_ip_user_agent"] = base64_encode($_SERVER['HTTP_USER_AGENT']);
    $sess_datas["id_session"] = $id_session;
    $obj = new obj($sess_datas);
    $ser = serialize($obj);
    $_SESSION["payload"] = $ser;
    
  $rows=$db->query("INSERT INTO `sessions` (`session_id`,`data`,`ip`,`agent`,`stamp`) VALUES ('".$id_session."','".$ser."','".getUserIpAddr()."','".$_SERVER['HTTP_USER_AGENT']."','".time()."');");
 
    $duree = time()+3600*24*7;
    $options = ["expires" => $duree, "path" => "/appliops_user_cookies/", "domain" => "appliops.com", "secure" => "TRUE", "httponly" => "TRUE", "samesite" => "Strict"];
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
    //$_SESSION["sss"] = (setcookie("appliops_user_cookie","a_u_c", $duree, "/appliops_user_cookies/", $domain, TRUE, TRUE))?$ser:"aucune datas";
    $_SESSION["visitor"] = base64_encode( $id_session );
 
    $cookie_file_save = $id_session;
    file_put_contents("./appliops_user_cookies/".$cookie_file_save.".txt", $ser );
}

/*********************************************************SESSION HANDLER**********************************************************/

$f3->route('GET /',
    function($f3, $params) {
        if (isset($_GET["logout"])){
           //var_dump(isset($_GET["logout"]));
           //vider session
           $id_session = session_id();
           unlink("appliops_user_cachecache/".$id_session.".ops");  
        }
        $params[1] = $_SESSION;
        $f3->set('title',translate(6));
        $f3->set('trusted_space',translate(1));
        $f3->set('id_w',translate(2));
        $f3->set('pw_w',translate(3));
        $f3->set('id_pl_w',translate(4));
        $f3->set('pw_pl_w',translate(3));
        $f3->set('login_w',translate(5));
        if ($_SESSION["app_error"] != ''){
           echo $_SESSION["app_error"]; 
           unset( $_SESSION["app_error"] ); 
        }
        echo View::instance()->render('ui/layout-login.htm');
    }
);

$f3->route('GET|POST /main',
    function($f3, $params) {
       global $id_db, $pw_db, $na_db;
       $params[1] = $_SESSION;
       $id_ok = false;
       $pw_ok = false;
       $login_atempt = false;
       $id_session = session_id();

       if (isset($_POST["username"])&&$_POST["username"]!=""){
          $id_ok = true;
       }
       if (isset($_POST["password"])&&$_POST["password"]!=""){
          $pw_ok = true;
       }
       if ($id_ok && $pw_ok || $_SESSION["logged_in"]){
          $login_atempt = true;

          $filename = "appliops_user_cachecache/".$id_session.".ops";
/*
          if (file_exists($filename)) {
              //echo "Le fichier $filename existe.";
              $datas_s = json_decode( file_get_contents("appliops_user_cachecache/".$id_session.".ops") );  
              //var_dump( base64_decode($datas_s[1]->password) );
              //var_dump( $datas_s );
              $_SESSION["password"] = base64_decode($datas_s[1]->password);   
          } else {
              echo "Le fichier $filename n'existe pas.";
          }
*/

          $db = new mysqli("localhost", $id_db, $pw_db, $na_db);

          if ( is_null($_POST["password"])  ){
             $datas_s = json_decode( file_get_contents("appliops_user_cachecache/".$id_session.".ops") );  
             $new_hashed_user_pw = crypt( base64_decode($datas_s[1]->password), '$5$rounds=5000$quietfunkyihsupinthishole$' );
             ////$new_hashed_user_pw = crypt( $_SESSION["password"], '$5$rounds=5000$quietfunkyihsupinthishole$' );
             //var_dump($new_hashed_user_pw);
             $rows=$db->query("SELECT `id_utilisateur`, `ville` FROM `utilisateurs` WHERE `pw` = SHA1('".base64_encode($new_hashed_user_pw)."');" );
             //echo "p not init"; 
             //$datas_s = json_decode( file_get_contents("appliops_user_cachecache/".$id_session.".ops") );  
             $row = $rows->fetch_assoc();
             //var_dump(base64_decode($row["id_utilisateur"]));
             //var_dump($datas_s[1]->username);
             ////var_dump($_SESSION);
             //$_SESSION["id_utilisateur"] = $row["id_utilisateur"];
             $_SESSION["ville"] = $row["ville"];
             //$f3->set('id_utilisateur',$_SESSION["id_utilisateur"]);
             $f3->set('ville',$_SESSION["ville"]);
          }else{
             $new_hashed_user_pw = crypt( $_POST["password"], '$5$rounds=5000$quietfunkyihsupinthishole$' );
             //var_dump($new_hashed_user_pw);
             $rows=$db->query("SELECT `id_utilisateur`, `ville` FROM `utilisateurs` WHERE `pw` = SHA1('".base64_encode($_POST["password"])."');" );
             //echo "p init"; 
             $row = $rows->fetch_assoc();
             //var_dump($row);
             $_SESSION["id_utilisateur"] = $row["id_utilisateur"];
             $_SESSION["ville"] = $row["ville"];
             $f3->set('id_utilisateur',$_SESSION["id_utilisateur"]);
             $f3->set('ville',$_SESSION["ville"]);
             //var_dump($row);
          }

          //$rows=$db->query("SELECT `id_utilisateur`, `ville` FROM `utilisateurs` WHERE `pw` = SHA1('".base64_encode($new_hashed_user_pw)."');" );




          //$rows=$db->query("SELECT `id_utilisateur`, `ville` FROM `utilisateurs` WHERE `pw` = SHA1('".base64_encode($_POST["password"])."');" );
          //$row = $rows->fetch_assoc();
          //var_dump($rows);

          $_SESSION["logged_in"] = true;
          //$_SESSION["id_utilisateur"] = $row["id_utilisateur"];
          ////$_SESSION["ville"] = $row["ville"];
          //var_dump($_SESSION);
       }else{
          //credentials not recognized redirect to log page
          $_SESSION["app_error"] = translate(7);
          $f3->reroute('/');
       }

       $home_visible="visible";
       $f3->set('home_visible',$home_visible);
       //$f3->set('id_utilisateur',$_SESSION["id_utilisateur"]);
       //$f3->set('ville',$_SESSION["ville"]);

       /******************************************************************displaying************************************************************************/ 

       $url = 'http://mail.appliops.fr/';

       $postVars = array(
           'username' => base64_encode($_POST["username"]),
           'password' => base64_encode($_POST["password"])
       );
       $options = array(
           'method'  => 'POST',
           'content' => http_build_query($postVars),
       );
       $result = \Web::instance()->request($url, $options);
       $datas = json_decode($result['body']);
       //var_dump($datas->a);

       /*reload*/
       //if ($datas->a == 51){
       if (!$datas){
          //echo "ko";
          $datas = json_decode( file_get_contents("appliops_user_cachecache/".$id_session.".ops") );
          //$f3->set('id_utilisateur',$_SESSION["id_utilisateur"]);
          //$f3->set('ville',$_SESSION["ville"]);
          //var_dump( $f3->get('id_utilisateur') );

          $datas = $datas[0];
          //var_dump($datas);
       }else{
          //echo "ok";
       }
       /*reload*/


       //$datas = json_decode($result['body']);
       $datas_r = array($datas,$postVars,array($_SESSION["id_utilisateur"],$_SESSION["ville"]) );
/*
       if (!file_exists("appliops_user_cachecache/".$id_session.".ops")) {       
          file_put_contents("appliops_user_cachecache/".$id_session.".ops", json_encode($datas_r) );
       }
*/
       //var_dump($datas->content);
       //var_dump($datas->headers);
       //var_dump($_POST);

       $mail_master = array();

       if ($_POST["submit"] != ""){
          foreach($_POST as $data_box => $val){
             $indicebox = substr($data_box, -1);
             //var_dump( intval($indicebox) );
          }
       }

       for($i=0;$i<count($datas->title);$i++){
          $tmp_labels[$i] = $datas->title[$i]->label_mailbox; 
          $tmp_nbmail[$i] = $datas->title[$i]->nbmail_mailbox; 

          if ( stripos($datas->title[$i]->label_mailbox, "Objets") !== false ){
               $tmp_labels[$i] =  "Objets envoyés";
          }else{
                if ( stripos($datas->title[$i]->label_mailbox, "INBOX") !== false ){
                     $tmp_labels[$i] =  "Courrier";
                }
          }
          //$mail_master[strtolower($tmp_labels[$i])] = array();
          $mail_master[$i] = array();
       }

       $t_t_l = array(2,0,3,4,1);
       $tampon_tmp_labels[0]= $tmp_labels[2];   
       $tampon_tmp_labels[1]= $tmp_labels[0];   
       $tampon_tmp_labels[2]= $tmp_labels[3];   
       $tampon_tmp_labels[3]= $tmp_labels[4];   
       $tampon_tmp_labels[4]= $tmp_labels[1];   
       $tmp_labels = $tampon_tmp_labels;     


/*
       for($i=0;$i<count($datas->content);$i++){
          if ( count($datas->content[$i][7]) != 0 || 1){
             array_push($mail_master[$datas->content[$i][1]->indice_mailbox], $datas->content[$i][0]);
             //var_dump( $datas->content[$i][1]->indice_mailbox );
             var_dump( $datas->content[$i][6] );

          }
       }
*/
       $mails_header = array();

       function sortFunction( $a, $b ) {
          return strtotime($a[1]->MailDate) - strtotime($b[1]->MailDate);
       }
       usort($datas->headers, "sortFunction");

       //for($i=0;$i<count($datas->headers);$i++){
       for($i=(count($datas->headers)-1);$i>=0;$i--){
             $date_mail = date_create( $datas->headers[$i][1]->MailDate );
             $date_mail = date_format($date_mail, 'Y-m-d H:i:s'); 
             $date_format_display = ((time()-strtotime($date_mail))/(24 * 60 * 60)<1.0); 
             if ( $date_format_display ){
                 $date_mail = substr($date_mail,11);
             }else{

             }
             if ($datas->headers[$i][0]->indice_mailbox == $t_t_l[intval($indicebox)] ){
             //if ($datas->headers[$i][0]->indice_mailbox == intval($indicebox) ){
             $tmp_mail_header = "<div class='from-mail-header'  style='float:left;'>".$datas->headers[$i][1]->from[0]->personal."</div><div class='date-mail-header' style='float:right;'>".$date_mail."</div><div class='subject-mail-header' style='display:block;clear:right;'>".imap_mime_header_decode($datas->headers[$i][1]->subject)[0]->text."</div>"; 
             $mails_header[$i] = "<div class='checkmark-item' style='float:left;padding:6px;display:block;'><input type='checkbox' id='m-h-c' name='m_h_c_'".$i."/></div><div class='mail-content-item'>".$tmp_mail_header."</div>"; 
             //var_dump( $datas->headers[$i][1]->from[0]->personal." ".imap_mime_header_decode($datas->headers[$i][1]->subject)[0]->text." ".$datas->headers[$i][1]->MailDate." ".$date_mail );
             }
       }

       $f3->set('mails_header',$mails_header);
       //var_dump($mail_master);

       $f3->set('dossiers_mail',$tmp_labels);
       $_SESSION["dossiers_mail"] = $tmp_labels;

       $f3->set('dossiers_mail_nbmail',$tmp_nbmail);
       $_SESSION["dossiers_mail_nbmail"] = $tmp_nbmail;


       //var_dump($f3->get('dossiers_mail_nbmail'));
       //var_dump($datas->title);
       //var_dump(json_decode($result['body']));

       $obj = new obj($_SESSION);
       $ser = serialize($obj);
       $_SESSION["payload"] = $ser;
       //$id_session = session_id();
       $rows=$db->query("INSERT INTO `sessions` (`session_id`,`data`,`ip`,`agent`, `stamp`) VALUES ('".$id_session."','".$ser."','".getUserIpAddr()."','".$_SERVER['HTTP_USER_AGENT']."','".time()."' ); ");

       /******************************************************************displaying************************************************************************/ 

       $datas_r = array($datas,$postVars,array($_SESSION["id_utilisateur"],$_SESSION["ville"]) );
       if (!file_exists("appliops_user_cachecache/".$id_session.".ops")) {
          file_put_contents("appliops_user_cachecache/".$id_session.".ops", json_encode($datas_r) );
       }
       //var_dump($datas_r);

      if ($_SESSION["app_error"] != ''){
           echo $_SESSION["app_error"];
           unset( $_SESSION["app_error"] );
      }
      //echo View::instance()->render('ui/layout-main.htm');
      echo \Template::instance()->render('ui/layout-main.htm');
    }
);

$f3->route('GET|POST /main/@mailbox',
    function($f3, $params) {
       global $id_db, $pw_db, $na_db;
       $params[1] = $_SESSION;
       $id_session = session_id();

       //var_dump($_SESSION);
       //var_dump($_POST);

       $datas_ = json_decode(file_get_contents("appliops_user_cachecache/".$id_session.".ops"));
       //var_dump($datas_);
       //var_dump($datas_[0]->headers[intval($params["mailbox"])]);
       //var_dump($datas_[1]->username);



       $url = 'http://mail.appliops.fr/';

       $postVars = array(
           'username' => ($datas_[1]->username),
           'password' => ($datas_[1]->password),
           'content_header' => ($datas_[0]->headers[intval($params["mailbox"])]),
           'indice_mail' => ( intval($params["mailbox"]) ),
           'read' => true
       );
       $options = array(
           'method'  => 'POST',
           'content' => http_build_query($postVars),
       );
       $result = \Web::instance()->request($url, $options);
       //var_dump($result["body"]);
       $dab = json_decode($result["body"]);
       $folder = "attachment";
       $mid = intval($params["mailbox"]) ;

      //var_dump($dab);
       //var_dump($dab->content[0][8]);
       //var_dump($dab->content[0][2]);
       $pj_h = explode(' ', json_encode($dab->content[0][2])); 
       for ($is=1; $is<=count($pj_h); $is++) {
           //var_dump($pj_h[$is]);
           if($pj_h[$is] == "application\/zip;"){ 
             //var_dump($pj_h[$is]);


           }
           elseif($pj_h[$is] == "image\/jpeg;"){ 
             //var_dump($pj_h[$is]);
             $filename_pj = explode('\r\n', $pj_h[$is+4]) ;
             $filename_pj = explode('=', $filename_pj[0]) ;
             $filename_pj = $filename_pj[1] ;
             $filename_pj_data = base64_decode($pj_h[$is+5]) ;
             $taille_filename_pj_data = strlen($filename_pj_data) ;

             ob_start();
             $page = ob_get_contents();
   ob_end_clean();
   $cwd = getcwd();
   $file = "$cwd" .'/attachment/'. $filename_pj;
   @chmod($file,0755);
   $fw = fopen($file, "w");
   fputs($fw,$filename_pj_data, ($taille_filename_pj_data));
   //fputs($fw,$page, ($taille_filename_pj_data));
   fclose($fw);  

             //var_dump( ($taille_filename_pj_data) );
//header("Content-type: image/png");
//header("Content-Length: ".$taille_filename_pj_data);
 
//list($width, $height, $type, $attr) = getimagesize($file);
//echo "<img src=".$file." ".$attr." alt='Exemple avec getimagesize()' />";


             //$filename_pj_data = $pj_h[$is+5] ;
             //var_dump( $filename_pj );
             //var_dump( $filename_pj_data );
             //var_dump( $taille_filename_pj_data."octectsss" );


//$source = "./". $folder ."/". $mid . "-" . $filename_pj;
//$src = ImageCreateFromJPEG($source);
//var_dump($src);
//$dst = ImageCreateTrueColor($tm_width,$tm_height);
//ImageCopyResized($dst, $src, 0, 0, 0, 0, $tm_width, $tm_height, $width, $height);
//ImageJPEG($dst, $destination); 

//header('Content-type: image/jpeg');//with header Content type 
//file_put_contents("./attachment/".$filename_pj, $filename_pj_data);
  

//$fp = fopen("./". $folder ."/". $mid . "-" . $filename_pj, "w");
//$fp = fopen("../../mail/". $folder ."/". $mid . "-" . $filename_pj, "w");
//fwrite($fp, $filename_pj_data);
//fclose($fp);



           }
       }

       //var_dump(explode(' ', json_encode($dab->content[0][2])) );

       $f3->set('mail_bd', htmlspecialchars(trim(strip_tags($dab->content[0][2]))) );





       if ( $_SESSION["logged_in"] ){
          $f3->set('dossiers_mail',$_SESSION["dossiers_mail"]);
          $f3->set('dossiers_mail_nbmail',$_SESSION["dossiers_mail_nbmail"]);
          if ($_POST["submit"] != ""){
             foreach($_POST as $data_box => $val){
                $indicebox = substr($data_box, -1);
                //var_dump( intval($indicebox) );
                //var_dump( json_decode($datas_) );
             }
          }


          //var_dump($_SESSION["dossiers_mail"]);
          //var_dump($f3->get('dossiers_mail'));
          //var_dump($_SESSION);
       }
       echo \Template::instance()->render('ui/layout-mtxain.htm');
    }
);


$f3->route('GET|POST /main/attachment/@img_id',
    function($f3, $params) {
       global $id_db, $pw_db, $na_db;
       $params[1] = $_SESSION;
       $id_session = session_id();

        $lines = (file('attachment/'.$params['img_id']) );
        $taille_filename_pj_data = filesize('attachment/'.$params['img_id']); 
        $_FILES["photo-img"] = $lines;
        $image  = $_FILES["photo-img"];
        $image  = implode("", $_FILES["photo-img"]);

        //var_dump(imagecreatefromstring($lines));
        //var_dump(imagecreatefromstring(base64_encode($image)));
        //var_dump($params);
        //echo "View::instance()->render";

$data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
       . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
       . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
       . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
$datass = $data;
//var_dump(($taille_filename_pj_data));
//$data = (implode("", $lines ));
//$data = (mb_convert_encoding($data, "UTF-8", "UTF7-IMAP"));
//$data = (mb_convert_encoding($data, "UTF7-IMAP", "UTF-8"));
//var_dump(base64_decode($image));
        //echo mb_detect_encoding($data); 

//$fp   = fopen('php://input', 'r');
$fp   = fopen('data://image/jpeg;base64,', 'r');
$meta = stream_get_meta_data($fp);
$data = (file_get_contents('attachment/'.$params['img_id'])); 
//$im = imagecreatefromstring("data://image/jpeg;base64," . base64_encode($data)); 
//echo imap_utf8($data);
//$im = imagecreatefromjpeg("data://image/jpeg;base64," . base64_decode($data)); 
//$im = imagecreatefromjpeg('attachment/'.$params['img_id']); 
// Affiche "text/plain"
//var_dump( $meta);

$datass = base64_encode($datass);

//$im = imagecreatefromjpeg(imap_utf8($data));
$im = imagecreatefromjpeg($datass);
if ($im !== false) {
    header('Content-Type: image/jpg');
    header("Content-Length: ".$taille_filename_pj_data);
    imagejpeg($im);
    imagedestroy($im);
}
else {
    echo 'An error occurred.';
}

        //echo View::instance()->render('ui/layout-login.htm');
    }
);


$f3->run();



?>
