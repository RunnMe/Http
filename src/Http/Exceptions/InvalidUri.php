<?php

namespace Runn\Http\Exceptions;

use Runn\Http\Exception;
use Throwable;

/**
 * Class InvalidUri
 * @package Runn\Http\Exceptions
 */
class InvalidUri extends Exception
{

    /** @var string */
    protected string $uri;

    /**
     * InvalidUri constructor.
     *
     * @param string $uri
     * @param string $message
     * @param int|string $code
     * @param Throwable|null $previous
     */
    public function __construct(string $uri, $message = "Invalid URI", $code = 0, Throwable $previous = null)
    {
        $this->uri = $uri;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

}
