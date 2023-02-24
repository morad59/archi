<?php
$config = require __DIR__.'/../config.php';

$pw_db = $config['pw_db'];
$id_db = $config['id_db'];
$na_db = $config['na_db'];
$db = new mysqli("localhost", $id_db, $pw_db, $na_db);

$new_user = "morad.derouich@mssante.appliops.fr";
$new_user_pw = "Mdhdf2021";
$new_user_ville = "Hazebrouck";


$new_hashed_user_pw = crypt( $new_user_pw, '$5$rounds=5000$quietfunkyihsupinthishole$' ); 
//$new_hashed_user_id = md5( $new_user, '$5$rounds=5000$quietfunkyihsupinthishole$' ); 
$new_hashed_user_id = md5( $new_user ); 
$rows=$db->query("INSERT INTO `utilisateurs` (`id_utilisateur`,`pw`,`ville`) VALUES ('".base64_encode($new_hashed_user_id)."',SHA1('".base64_encode($new_hashed_user_pw)."'),'".$new_user_ville."');");

var_dump($rows);



//$rows=$db->query("SELECT `ville` FROM `utilisateurs` WHERE `pw` = SHA1('".base64_encode( crypt( $new_user_pw, '$5$rounds=5000$quietfunkyihsupinthishole$' ) )."');" );






?>
