<?php

namespace Opengeek\Exceptions;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class HttpException extends \Exception
{
    private array $body;

    public function __construct(
        private readonly ServerRequestInterface $request,
        private readonly int $statusCode,
        private readonly string $title,
        private readonly string $description = '',
        private readonly array $properties = [],
        private readonly array $headers = [],
        ?Throwable $previous = null
    ) {
        $code = $statusCode;
        if (isset($body['code'])) {
            $code = (int)$body['code'];
        } elseif ($previous) {
            $code = $previous->getCode();
        }

        $message = !empty($description) ? $description : $title;

        parent::__construct($message, $code, $previous);

        $this->body = array_merge(
            [
                'status' => $statusCode,
                'title' => $title,
                'description' => $description
            ],
            $properties
        );
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param ServerRequestInterface $request
     * @param \Exception $e
     * @return static
     */
    public static function from(ServerRequestInterface $request, Throwable $e): static
    {
        if (!$e instanceof Exception) {
            $e = Exception::from($e);
        }

        $body = [];
        if ($e->getTitle()) {
            $body['title'] = $e->getTitle();
        }
        $body = array_merge($body, $e->getProperties());

        return match ($e->getCode()) {
            400 => static::badRequest($request, $body, [], $e->getPrevious()),
            401 => static::unauthorized($request, $body, [], $e->getPrevious()),
            403 => static::forbidden($request, $body, [], $e->getPrevious()),
            404 => static::notFound($request, $body, [], $e->getPrevious()),
            405 => static::methodNotAllowed($request, $body, [], $e->getPrevious()),
            409 => static::conflict($request, $body, [], $e->getPrevious()),
            415 => static::unsupportedMediaType($request, $body, [], $e->getPrevious()),
            default => static::internalServerError($request, $body, [], $e->getPrevious()),
        };
    }

    public static function fromHttpException(\Slim\Exception\HttpException $e): static
    {
        $body = [
            'title' => $e->getTitle(),
            'description' => $e->getDescription(),
            'detail' => $e->getMessage()
        ];

        return match ($e->getCode()) {
            400 => static::badRequest($e->getRequest(), $body, [], $e),
            401 => static::unauthorized($e->getRequest(), $body, [], $e),
            403 => static::forbidden($e->getRequest(), $body, [], $e),
            404 => static::notFound($e->getRequest(), $body, [], $e),
            405 => static::methodNotAllowed($e->getRequest(), $body, [], $e),
            409 => static::conflict($e->getRequest(), $body, [], $e),
            415 => static::unsupportedMediaType($e->getRequest(), $body, [], $e),
            501 => static::notImplemented($e->getRequest(), $body, [], $e),
            default => static::internalServerError($e->getRequest(), $body, [], $e),
        };
    }

    public static function badRequest(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            400,
            'Bad Request',
            'The request could not be understood by the server due to malformed syntax. DO NOT repeat the request without modifications.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.1'], $body),
            $headers,
            $previous
        );
    }

    public static function unauthorized(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            401,
            'Unauthorized',
            'The requested resource requires authentication.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.2'], $body),
            $headers,
            $previous
        );
    }

    public static function forbidden(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            403,
            'Forbidden',
            'Access to the requested resource is forbidden.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.4'], $body),
            $headers,
            $previous
        );
    }

    public static function notFound(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            404,
            'Not Found',
            'The requested resource was not found on this server.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.5'], $body),
            $headers,
            $previous
        );
    }

    public static function methodNotAllowed(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            405,
            'Method Not Allowed',
            'The requested method is not supported by this resource.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.6'], $body),
            $headers,
            $previous
        );
    }

    public static function conflict(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            409,
            'Conflict',
            'The request could not be completed due to a conflict with the current state of the resource.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.10'], $body),
            $headers,
            $previous
        );
    }

    public static function unsupportedMediaType(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            415,
            'Unsupported Media Type',
            'The server is refusing to service the request because the entity of the request is in a format not supported by the requested resource for the requested method.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.4.16'], $body),
            $headers,
            $previous
        );
    }

    public static function internalServerError(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            500,
            'Internal Server Error',
            'The server encountered an unexpected condition that prevented it from fulfilling the request.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.1'], $body),
            $headers,
            $previous
        );
    }

    public static function notImplemented(
        ServerRequestInterface $request,
        array $body = [],
        array $headers = [],
        Throwable|null $previous = null
    ): static {
        return new static(
            $request,
            501,
            'Not Implemented',
            'The server does not support the functionality required to fulfill the request.',
            array_merge(['type' => 'https://datatracker.ietf.org/doc/html/rfc2616#section-10.5.2'], $body),
            $headers,
            $previous
        );
    }
}
