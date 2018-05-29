
<?php

require 'vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$client = new \GuzzleHttp\Client();
$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) use ($client) {
    
    $res = $client->request('GET', 'https://api.github.com/repos/xocasdashdash/php-async');
    $output = $res->getBody();

    return new React\Http\Response(
        200,
        array('Content-Type' => 'application/json'),
        $output
    );
});

$socket = new React\Socket\Server(8080, $loop);
$server->listen($socket);

echo "Server running at http://127.0.0.1:8080\n";

$loop->run();
