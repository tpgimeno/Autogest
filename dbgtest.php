<?php



$address = '127.0.0.1';
$port = '9000';
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_bind($sock, $address, $port) or die();
socket_listen($sock);
$client = socket_accept($sock);
echo "Connection established: $client";
socket_close($client);
socket_close($sock);
