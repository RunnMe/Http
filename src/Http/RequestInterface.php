<?php

namespace Runn\Http;

/**
 * Interface RequestInterface
 * @package Runn\Http
 */
interface RequestInterface extends \Psr\Http\Message\ServerRequestInterface
{
    /**
     * Creates object from $_SERVER, $_REQUEST, $_COOKIE, $_FILES
     * @return RequestInterface
     */
    public static function constructFromGlobals(): RequestInterface;

}
