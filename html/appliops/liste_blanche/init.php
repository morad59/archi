<?php

$arrContextOptions=array(
    "ssl"=>array(
       "verify_peer"=>false,
       "verify_peer_name"=>false,
    ),
);

$image_liste_blanche = file_get_contents('https://espacedeconfiance.mssante.fr/listeblanchemssante.xml', false, stream_context_create($arrContextOptions));  
file_put_contents('/var/www/html/appliops/liste_blanche/liste_blanche_'.date('YmdHMS').'.xml', $image_liste_blanche);






?>
