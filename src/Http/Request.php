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
    /** @var array $routeParams Uri params */
    protected array $routeParams;

    /**
     * Add route param
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
     * Get all route params
     *
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * Get param by key
     *
     * @param string $key
     * @return mixed|null
     */
    public function getRouteParam(string $key)
    {
        return $this->routeParams[$key] ?? null;
    }

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
