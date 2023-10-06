<?php

return static function(string $appEnv) {
    $settings = [
        'app_env' => $appEnv,
        'app_name' => $_ENV['APP_NAME'] ?? 'Minimal Slim Skeleton',
        'app_version' => version(),
        'di_compilation_path' => __DIR__ . '/../var/compiled',
        'display_error_details' => false,
        'log_errors' => true,

        'twig' => [
            'cache' => __DIR__ . '/../var/twig/cache'
        ],
    ];

    if ($appEnv === 'DEVELOPMENT' || $appEnv === 'TEST') {
        $settings['di_compilation_path'] = '';
        $settings['display_error_details'] = true;
    }

    return $settings;
};
