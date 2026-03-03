<?php

declare(strict_types=1);

namespace Opengeek\Handlers;

use Opengeek\Exceptions\HttpException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigRuntimeLoader;
use Throwable;
use Twig\Error\Error;

readonly class ErrorHandler
{
    public function __construct(
        private App $app,
        private Twig $twig,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails,
        ?LoggerInterface $logger = null
    ): ResponseInterface {
        if (!$exception instanceof HttpException) {
            if ($exception instanceof \Slim\Exception\HttpException) {
                $exception = HttpException::fromHttpException($exception);
            } else {
                $exception = HttpException::from($request, $exception);
            }
        }

        $response = $this->app->getResponseFactory()->createResponse();

        $body = $exception->getBody();
        if ($displayErrorDetails) {
            $body['exception'] = [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'uri' => (string)$exception->getRequest()->getUri(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        if ($logErrors && $this->logger instanceof LoggerInterface) {
            $logData = $body;
            if ($logErrorDetails) {
                $logData['exception'] = [
                    'message' => $exception->getMessage(),
                    'code' => $exception->getCode(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'uri' => (string)$exception->getRequest()->getUri(),
                    'trace' => $exception->getTrace(),
                ];
            }

            $this->logger->error($exception->getTitle() . ': ' . $exception->getMessage(), $logData);
        }

        foreach ($exception->getHeaders() as $key => $value) {
            $response = $response->withHeader($key, $value);
        }

        $responder = $exception->getRequest()->getAttribute('responder', 'view');
        if ($responder === 'json') {
            $json = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            if ($json === false) {
                $json = json_encode(
                    [
                        'json_error_code' => json_last_error(),
                        'json_error_message' => json_last_error_msg()
                    ],
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                );
            }

            $response->getBody()->write($json);
            $response = $response->withStatus($exception->getStatusCode());

            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->withHeader('Content-Type', 'text/html');

        $runtimeLoader = new TwigRuntimeLoader(
            $this->app->getRouteCollector()->getRouteParser(),
            $exception->getRequest()->getUri(),
            $this->app->getBasePath()
        );
        $this->twig->addRuntimeLoader($runtimeLoader);

        try {
            return $this->twig->render($response, 'error.twig', $body);
        } catch (Error $e) {
            if ($logErrors && $this->logger instanceof LoggerInterface) {
                $this->logger->error($e->getMessage(), $logErrorDetails ? ['exception' => $e] : []);
            }

            $errorMessage = "<pre>{$e->getMessage()}</pre>";
            if ($displayErrorDetails) {
                $errorMessage .= "<br><pre>{$e->getTraceAsString()}</pre>";
            }

            $response->getBody()->write($errorMessage);

            return $response->withStatus(500);
        }
    }
}
