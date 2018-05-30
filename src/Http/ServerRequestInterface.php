<?php

namespace Runn\Http;

/**
 * Interface ServerRequestInterface
 * @package Runn\Http
 */
interface ServerRequestInterface extends \Psr\Http\Message\ServerRequestInterface
{
    const PHP_INPUT = 'php://input';

    /**
     * Creates object from $_SERVER and php://input
     * @param array|null $server
     * @param string $stream
     * @return ServerRequestInterface
     */
    public static function constructFromGlobals(
        array $server = null,
        string $stream = self::PHP_INPUT
    ): ServerRequestInterface;
}
