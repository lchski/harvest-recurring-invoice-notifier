<?php
// Routes

$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
    // Render response
    return $response->write("Hello!");
});
