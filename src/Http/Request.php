<?php

namespace Runn\Http;

use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Stream;

/**
 * Class Request
 * @package Runn\Http
 */
class Request extends ServerRequest implements ServerRequestInterface
{
    use MarshalParametersTrait;

    /**
     * Creates object from $_SERVER, $_REQUEST, $_COOKIE, $_FILES
     * @return ServerRequestInterface
     * @throws Exceptions\InvalidUri
     */
    public static function constructFromGlobals(): ServerRequestInterface
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $headers = self::marshalHeaders();
        $uri = static::marshalUri($headers);
        $body = new Stream(fopen('php://input', 'rb'));
        $version = self::marshalProtocolVersion();

        return new static($method, $uri, $headers, $body, $version);
    }

    public function __construct(
        string $method,
        $uri,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        array $serverParams = []
    ) {
        parent::__construct($method, $uri, $headers, $body, $version, $serverParams);
    }
}
