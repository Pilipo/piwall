<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->post('/check', function (Request $req, Response $res, array $args){
    $this->logger->info("Checking in...");

    $data = $req->getParsedBody();

    $serial = $data['serial'];

    $res->getBody()->write($serial);

    // Check supplied serial number against db
    // What db?
    // if exists, reply "Nice to see you again..."
    // if doesn't exist, store RSA key and serial in db (mark entry unconfirmed, untested)
    //  then reply with RSA key for addition to authorized keys and wait for response.
});



$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});
