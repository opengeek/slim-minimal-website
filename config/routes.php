<?php

return function (\Slim\App $app) {
    $app->get('/[{name}/]', \Opengeek\Slim\Hello::class)->setName('Hello');
};
