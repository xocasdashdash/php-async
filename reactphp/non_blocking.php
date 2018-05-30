
<?php

require 'vendor/autoload.php';

use React\Stream\DuplexResourceStream;

use React\Stream\ThroughStream;
$loop = React\EventLoop\Factory::create();
$client = new React\HttpClient\Client($loop);

$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) use ($client) {
    $request = $client->request('GET', 'https://api.github.com/repos/xocasdashdash/php-async');
    $stream = new ThroughStream();
    
    $request->on('response', function (\React\HttpClient\Response $response) use ($stream) {
        echo "Received data...\n";
        $response->on('data', function ($chunk) use ($stream) {
            echo "Received data...\n";
            $stream->write($chunk);
        });
        $response->on('end', function()use($stream) {
            echo "DONE\n";
            $stream->end();
        });
        
    });

    $request->on('error',function() use($stream){
        echo "finished...";
        $stream->end();
    });
    $request->end();

    return new React\Http\Response(
        200,
        array('Content-Type' => 'application/json'),
        $stream
    );
});

$socket = new React\Socket\Server(8080, $loop);
$server->listen($socket);

echo "Server running at http://127.0.0.1:8080\n";

$loop->run();
