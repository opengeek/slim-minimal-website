<?php

namespace Opengeek\Exceptions;

use Throwable;

class Exception extends \Exception
{
    public static function from(Throwable $exception): static
    {
        if ($exception instanceof Exception) {
            return new static(
                '',
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getProperties(),
                $exception
            );
        }

        return new static(
            '',
            $exception->getMessage()
        );
    }

    public function __construct(
        private readonly string $title,
        string $message = '',
        int $code = 0,
        private readonly array $properties = [],
        Throwable|null $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
