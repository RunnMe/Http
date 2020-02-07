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
        /** @var string $actualLink string URI */
        $actualLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $uri = new Uri($actualLink);
        parse_str($uri->getQuery(), $query);
        $stream = (new StreamFactory())->createStream(file_get_contents('php://input'));
        return new Request(
            $_SERVER['REQUEST_METHOD'],
            $uri,
            new Headers(getallheaders()),
            $_COOKIE,
            $query,
            $stream
        );
    }
}
