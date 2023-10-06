<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->safeLoad();

define('APP_ENV', $_ENV['APP_ENV'] ?? 'DEVELOPMENT');
$settings = (require __DIR__ . '/../config/settings.php')(APP_ENV);

$containerBuilder = new \DI\ContainerBuilder();
if ($settings['di_compilation_path']) {
    $containerBuilder->enableCompilation($settings['di_compilation_path']);
}
(require __DIR__ . '/../config/dependencies.php')($containerBuilder, $settings);

\Slim\Factory\AppFactory::setContainer($containerBuilder->build());
$app = \Slim\Factory\AppFactory::create();

if ($settings['base_path'] ?? false) {
    $app->setBasePath($settings['base_path']);
}

$app->getRouteCollector()->setDefaultInvocationStrategy(new \Slim\Handlers\Strategies\RequestHandler(true));

(require __DIR__ . '/../config/middleware.php')($app);

(require __DIR__ . '/../config/routes.php')($app);

$app->run();
