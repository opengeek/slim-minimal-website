<?php

return static function (\Slim\App $app) {
    $app->add(\Slim\Views\TwigMiddleware::createFromContainer($app, \Slim\Views\Twig::class));

    $app->addRoutingMiddleware();
    $app->addBodyParsingMiddleware();

    $app->addErrorMiddleware($app->getContainer()->get('settings')['display_error_details'], true, true);
};
