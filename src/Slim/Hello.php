<?php

namespace Opengeek\Slim;

use Opengeek\Configuration;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Hello
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly Twig $twig
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();

        $name = $request->getAttribute('name', 'World');

        return $this->twig->render($response, 'index.twig', [
            'name' => $name,
            'app_name' => $this->configuration->get('app_name'),
            'app_version' => $this->configuration->get('app_version'),
        ]);
    }
}
