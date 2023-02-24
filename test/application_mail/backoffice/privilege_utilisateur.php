<?php
$config = require __DIR__.'/../config.php';

$pw_db = $config['pw_db'];
$id_db = $config['id_db'];
$na_db = $config['na_db'];
$db = new mysqli("localhost", $id_db, $pw_db, $na_db);

$new_user = "morad.derouich@mssante.appliops.fr";
$new_user_pw = "Mdhdf2021";
$new_user_ville = "Hazebrouck";

//$rows=$db->query("SELECT `ville` FROM `utilisateurs` WHERE `pw` = SHA1('".base64_encode($new_user_pw)."');" );
$rows=$db->query("SELECT `ville` FROM `utilisateurs` WHERE `pw` = SHA1('".base64_encode( crypt( $new_user_pw, '$5$rounds=5000$quietfunkyihsupinthishole$' ) )."');" );

//var_dump($rows);
$row = $rows->fetch_assoc();
var_dump($row);










?>
