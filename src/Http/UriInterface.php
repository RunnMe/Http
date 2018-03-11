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
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme(): string;

    /**
     * Alias for $this->getScheme()
     * @return string
     */
    public function getProtocol(): string;

    /**
     * User name or empty string
     * @return string
     */
    public function getUserName(): string;

    /**
     * Password or empty string
     * @return string
     */
    public function getPassword(): string;

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo(): string;

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
