<?php

if (!function_exists('Opengeek\version')) {
    function version(): string
    {
        return $_ENV['APP_VERSION'] ?? trim(exec('git log --pretty="%h" -n1 HEAD')) ?? 'unknown';
    }
}
