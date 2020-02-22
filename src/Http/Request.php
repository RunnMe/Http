<?php

namespace Runn\Http;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Interfaces\HeadersInterface;
use Slim\Psr7\Stream;

/**
 * Base HTTP request class
 *
 * Class Request
 * @package Runn\Http
 */
class Request extends \Slim\Psr7\Request implements RequestInterface
{

    /** @var array $routeParams URI params */
    protected array $routeParams = [];

    public function __construct(
        $method = 'GET',
        UriInterface $uri = null,
        HeadersInterface $headers = null,
        array $cookies = [],
        array $serverParams = [],
        StreamInterface $body = null,
        array $uploadedFiles = []
    )
    {
        if (null === $uri) {
            $uri = new Uri('');
        }

        if (null === $headers) {
            $headers = new Headers();
        }

        if (null === $body) {
            $body = new Stream(fopen('php://input', 'r'));
        }

        parent::__construct($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);
    }

    /**
     * Adds one route param
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addRouteParam(string $key, $value)
    {
        $this->routeParams[$key] = $value;
        return $this;
    }

    /**
     * Returns all route params
     *
     * @return array
     */
    public function getRouteParams(): array
    {
        return $this->routeParams;
    }

    /**
     * Returns route parameter by key
     *
     * @param string $key
     * @return mixed|null
     */
    public function getRouteParam(string $key)
    {
        return $this->routeParams[$key] ?? null;
    }

    /**
     * Creates request object from $_SERVER, $_REQUEST, $_COOKIE, $_FILES
     *
     * @return RequestInterface
     * @throws Exceptions\InvalidUri
     */
    public static function constructFromGlobals(): RequestInterface
    {
        $method = $_SERVER['REQUEST_METHOD'];

        $protocol = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';

        /** @var string $actualLink string URI */
        $actualLink = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        /** @var Uri $uri */
        $uri = new Uri($actualLink);

        $headers = new Headers(getallheaders());

        $cookies = $_COOKIE;

        $serverParams = $uri->getQueryParams()->toArray();

        $stream = (new StreamFactory())->createStream(file_get_contents('php://input'));

        $uploadedFiles = $_FILES;

        return new static(
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
