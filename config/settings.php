<?php

declare(strict_types=1);

return static function(string $appEnv) {
    $settings = [
        'app_env' => $appEnv,
        'app_name' => $_ENV['APP_NAME'] ?? 'Minimal Slim Skeleton',
        'app_version' => version(),
        'di_compilation_path' => __DIR__ . '/../var/compiled',
        'display_error_details' => false,
        'log_error_details' => false,
        'log_errors' => true,

        'logger' => [
            'name' => 'web',
            'path' => __DIR__ . '/../var/log/error.log',
            'level' => \Monolog\Logger::ERROR,
            'max_files' => 7,
            'use_locking' => true,
        ],

        'twig' => [
            'cache' => __DIR__ . '/../var/twig/cache'
        ],
    ];

    if ($appEnv === 'DEVELOPMENT' || $appEnv === 'TEST') {
        $settings['di_compilation_path'] = '';
        $settings['display_error_details'] = true;
        $settings['twig']['cache'] = false;
        $settings['logger']['level'] = \Monolog\Logger::DEBUG;
        $settings['logger']['use_locking'] = false;
    }

    $settings['caches'] = [
        $settings['di_compilation_path'],
        $settings['twig']['cache'],
    ];

    return $settings;
};
