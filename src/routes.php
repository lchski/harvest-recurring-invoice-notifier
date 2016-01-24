<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, $args) {
    // Render response
    return $response->write("Hello!");
});

$app->post('/invoice/new', function(Request $request, Response $response, $args) {
    
})->add(function(Request $request, Response $response, $next) {
    // Only continue if our payload is JSON from Zapier.
    if ( $request->getMediaType() == 'application/json' && $request->getHeader('User-Agent')[0] == 'Zapier' ) {
        $response = $next($request, $response);

        return $response;
    }

    // Error out, because our payload is invalid.
    return $response
        ->withStatus(400, "Data must be JSON from Zapier.");
})->add(function(Request $request, Response $response, $next) {
    // Retrieve our JSON payload as a PHP array.
    $payload = $request->getParsedBody();

    if (isset($payload['recurring_invoice_id'])) {
        $response = $next($request, $response);

        return $response;
    }

    return $response
        ->withStatus(200, "Invoice not a recurring one.");
});
