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
     */
    public static function constructFromGlobals(
        array $server = null,
        string $stream = self::PHP_INPUT
    ): ServerRequestInterface {
        $server = self::normalizeServer($server ?? $_SERVER);
//        $request = $request ?? $_REQUEST;
//        $files = self::normalizeFiles($files ?? $_FILES);

        $headers = self::marshalHeaders($server);
//        if (null === $cookie && array_key_exists('cookie', $headers)) {
//            $cookie = self::parseCookieHeader($headers['cookie']);
//        }

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
