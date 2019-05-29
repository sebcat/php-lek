<?php

namespace App;

use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\RequestInterface;

class Hello
{
  private $response;
  public function __construct(ResponseInterface $response) {
    $this->response = $response;
  }

  public function __invoke(RequestInterface $request) : ResponseInterface {
    $name = htmlspecialchars($request->getAttribute('name'));
    $response = $this->response->withHeader('Content-Type', 'text/html');
    $response->getBody()->write(
        "<html><head></head><body>{$name}</body></html>");
    return $response;
  }
}

