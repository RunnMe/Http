<?php

namespace Runn\Http;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;

/**
 * Interface RequestInterface
 * @package Runn\Http
 */
interface RequestInterface extends PsrServerRequestInterface
{
    /**
     * Adds one route param
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function addRouteParam(string $key, $value);

    /**
     * Returns all route params
     *
     * @return array
     */
    public function getRouteParams(): array;

    /**
     * Returns route parameter by key
     *
     * @param string $key
     * @return mixed|null
     */
    public function getRouteParam(string $key);

    /**
     * Creates object from $_SERVER, $_REQUEST, $_COOKIE, $_FILES
     * @return RequestInterface
     */
    public static function constructFromGlobals(): RequestInterface;

}
