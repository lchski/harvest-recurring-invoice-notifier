<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/', function (Request $request, Response $response, $args) {
    // Render response
    return $response->write("Hello!");
});

$app->post('/invoice/new', function(Request $request, Response $response, $args) {
    // Retrieve our JSON payload as a PHP array.
    $payload = $request->getParsedBody();

    $this->mailer
        ->setTo('lucas+harvestnotifier@ecustom.ca', 'Lucas Cherkewski')
        ->setSubject('Harvest Recurring Invoice Created: ' . $payload['subject'])
        ->setFrom('lucas+harvestnotifier@ecustom.ca', 'Harvest Notifier (ecustom.ca)')
        ->addMailHeader('Reply-To', 'lucas+harvestnotifier@ecustom.ca', 'Harvest Notifier (ecustom.ca)')
        ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
        ->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
        ->setMessage('<p>Invoice for ' . $payload['client_name'] . ', due ' . date('F j Y', strtotime($payload['due_at'])) . '.</p><p>View the invoice <a href="https://lucascherkewski.harvestapp.com/invoices/' . $payload['id'] . '">here</a>.</p><p><small>This email generated automatically by the <a href="https://github.com/lchski/harvest-recurring-invoice-notifier">Harvest Recurring Invoice Notifier</a> running at <a href="http://ecustom.ca/harvest-webhook/">http://ecustom.ca/harvest-webhook/</a>.</small></p>')
        ->setWrap(100);
    $send = $this->mailer->send();

    if ($send == true) {
        return $response->withStatus(200, "Email sent successfully.");
    } else {
        return $response->withStatus(500, "SimpleMail could not send the email.");
    }
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
