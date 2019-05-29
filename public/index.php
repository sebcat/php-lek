<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$reqbuilder = new \Nyholm\Psr7Server\ServerRequestCreator(
    $psr17Factory, // ServerRequestFactory
    $psr17Factory, // UriFactory
    $psr17Factory, // UploadedFileFactory
    $psr17Factory  // StreamFactory
);

$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->addDefinitions([
    \App\Hello::class => DI\create(\App\Hello::class)->constructor(DI\get('Response')),
    'Response' => DI\get(\Nyholm\Psr7\Response::class),
]);
$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
  $r->get('/hello/{name}', \App\Hello::class);
});

$middlewares = [
  new Middlewares\FastRoute($dispatcher),
  new Middlewares\RequestHandler($container),
];

$relay = new Relay\Relay($middlewares);
$req = $reqbuilder->fromGlobals();
$resp = $relay->handle($req);
(new Narrowspark\HttpEmitter\SapiEmitter())->emit($resp);

