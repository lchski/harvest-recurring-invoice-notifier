<?php
// Routes

$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    $this->mailer
        ->setTo('lucas.cherkewski@gmail.com', 'Your Email')
        ->setSubject('Test Message 2')
        ->setFrom('no-reply@domain.com', 'Domain.com')
        ->addMailHeader('Reply-To', 'no-reply@domain.com', 'Domain.com')
        ->addMailHeader('Cc', 'bill@example.com', 'Bill Gates')
        ->addMailHeader('Bcc', 'steve@example.com', 'Steve Jobs')
        ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
        ->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
        ->setMessage('<strong>This is a different test message.</strong>')
        ->setWrap(100);
    $send = $this->mailer->send();
    echo ($send) ? 'Email sent successfully' : 'Could not send email';

    // Render response
    return $response->write("Hello!");
});
