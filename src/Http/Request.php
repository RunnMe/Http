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
     * Creates object from $_SERVER and php://input
     * @param array|null $server
     * @param string $stream
     * @return ServerRequestInterface
     * @throws Exceptions\InvalidUri
     * @throws Exceptions\UnexpectedValueException
     */
    public static function constructFromGlobals(
        array $server = null,
        string $stream = self::PHP_INPUT
    ): ServerRequestInterface {
        $server = self::normalizeServer($server ?? $_SERVER);
        $headers = self::marshalHeaders($server);

        $method = $server['REQUEST_METHOD'] ?? 'GET';
        $uri = static::marshalUriFromServer($server, $headers);
        $body = new Stream(fopen($stream, 'rb'));
        $version = self::marshalProtocolVersion($server);

        return new static($method, $uri, $headers, $body, $version, $server);
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
