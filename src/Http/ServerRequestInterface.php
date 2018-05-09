<?php

namespace Runn\Http;

/**
 * Interface ServerRequestInterface
 * @package Runn\Http
 */
interface ServerRequestInterface extends \Psr\Http\Message\ServerRequestInterface
{
    /**
     * Creates object from $_SERVER, $_REQUEST, $_COOKIE, $_FILES
     * @return ServerRequestInterface
     */
    public static function constructFromGlobals(): ServerRequestInterface;

}
