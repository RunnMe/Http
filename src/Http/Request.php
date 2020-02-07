<?php

namespace Runn\Http;

use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;

/**
 * Class Request
 * @package Runn\Http
 */
class Request extends \Slim\Psr7\Request
{
    /**
     * @return static
     * @throws Exceptions\InvalidUri
     */
    public static function createFromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        /** @var string $actualLink string URI */
        $actualLink = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        /** @var Uri $uri */

        $uri = new Uri($actualLink);

        $headers = new  Headers(getallheaders());

        $cookies = $_COOKIE;

        $serverParams = $uri->getQueryParams()->toArray();

        $stream = (new StreamFactory())->createStream(file_get_contents('php://input'));

        $uploadedFiles = $_FILES;

        return new Request(
            $method,
            $uri,
            $headers,
            $cookies,
            $serverParams,
            $stream,
            $uploadedFiles
        );
    }
}
