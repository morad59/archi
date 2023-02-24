<?php
//namespace \Wrench\Application\DataHandlerInterface ;
require __DIR__.'/vendor/autoload.php';   

$server = new WebSocket\Server();
$server->accept();
$message = $server->receive();
$server->text($message);
$server->close();



?>
