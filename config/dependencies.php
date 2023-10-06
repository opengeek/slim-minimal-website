<?php

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
        ]
    );
};
