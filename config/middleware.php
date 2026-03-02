<?php

return static function (\Slim\App $app) {
    $app->add(\Slim\Views\TwigMiddleware::createFromContainer($app, \Slim\Views\Twig::class));

    $app->addRoutingMiddleware();
    $app->addBodyParsingMiddleware();

    $errorHandler = new \Opengeek\Handlers\ErrorHandler(
        $app,
        $app->getContainer()->get(\Slim\Views\Twig::class),
        $app->getContainer()->get(\Psr\Log\LoggerInterface::class)
    );

    $settings = $app->getContainer()->get('settings');
    $errorMiddleware = $app->addErrorMiddleware(
        $settings['display_error_details'],
        $settings['log_errors'],
        $settings['log_error_details'],
        $app->getContainer()->get(\Psr\Log\LoggerInterface::class)
    );
    $errorMiddleware->setDefaultErrorHandler($errorHandler);
};
