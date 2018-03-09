<?php

namespace Runn\Http;

/**
 * Value object representing an URI
 * Immutable
 *
 * Interface UriInterface
 * @package Runn\Http
 */
interface UriInterface extends \Psr\Http\Message\UriInterface
{

    /**
     * Alias for getScheme()
     * @return string
     */
    public function getProtocol(): string;

    /**
     * @return string
     */
    public function getUserName(): string;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @7.1
     * @return iterable
     */
    public function getQueryParams()/*: iterable*/;

    /**
     * @param $protocol
     * @return static
     */
    public function withProtocol(string $protocol);

    /**
     * @return static
     */
    public function withoutUserInfo();

    /**
     * @param string$userName
     * @return static
     */
    public function withUserName(string $userName);

    /**
     * @param string $password
     * @return static
     */
    public function withPassword(string $password);

    /**
     * @return static
     */
    public function withoutPassword();

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function withQueryParam(string $name, $value);

    /**
     * @param string $name
     * @return static
     */
    public function withoutQueryParam(string $name);

}
