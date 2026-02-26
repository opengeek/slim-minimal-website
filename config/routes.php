<?php

return function (\Slim\App $app) {
    $app->get('/[{name}/]', \Opengeek\Controllers\Home::class)->setName('Home');
};
