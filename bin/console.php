<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config');
$dotenv->safeLoad();

define('APP_ENV', $_ENV['APP_ENV'] ?? 'DEVELOPMENT');
$settings = (require __DIR__ . '/../config/settings.php')(APP_ENV);
$settings = (require __DIR__ . '/../config/bin/settings.php')($settings);

$containerBuilder = new \DI\ContainerBuilder();
if ($settings['di_compilation_path']) {
    $containerBuilder->enableCompilation($settings['di_compilation_path']);
}
(require __DIR__ . '/../config/dependencies.php')($containerBuilder, $settings);
(require __DIR__ . '/../config/bin/dependencies.php')($containerBuilder);

$container = $containerBuilder->build();

$app = new \Silly\Application($settings['app_name'], $settings['app_version']);

$app->useContainer($container, true, true);

$app->command('cache:clear', [\Opengeek\Console\Cache::class, 'clear'])
    ->descriptions('Clear caches');

$app->run();
