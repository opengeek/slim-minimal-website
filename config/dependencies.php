<?php

declare(strict_types=1);

return static function (\DI\ContainerBuilder $containerBuilder, array $settings) {
    $containerBuilder->addDefinitions(
        [
            'settings' => $settings,

            \Opengeek\Configuration::class => function (\Psr\Container\ContainerInterface $c) {
                return new \Opengeek\Configuration($c->get('settings'));
            },

            \Slim\Views\Twig::class => function (\Psr\Container\ContainerInterface $c) {
                $settings = $c->get('settings');

                $twigSettings = $settings['twig'];

                $twig = \Slim\Views\Twig::create(__DIR__ . '/../templates', $twigSettings);

                return $twig;
            },

            \Psr\Log\LoggerInterface::class => function (\Psr\Container\ContainerInterface $c) {
                $settings = $c->get('settings');

                $loggerSettings = $settings['logger'];
                $logger = new \Monolog\Logger($loggerSettings['name']);

                $handler = new \Monolog\Handler\RotatingFileHandler(
                    $loggerSettings['path'],
                    (int) ($loggerSettings['max_files'] ?? 7),
                    $loggerSettings['level'] ?? \Monolog\Logger::ERROR,
                    true,
                    null,
                    $loggerSettings['use_locking'] ?? false
                );
                $logger->pushHandler($handler);

                return $logger;
            },
        ]
    );
};
