<?php

namespace Runn\Http;

use Runn\Core\Std;
use Runn\Http\Exceptions\InvalidUrl;

/**
 * Class Uri
 * @package Runn\Http
 */
class Uri implements UriInterface
{

    protected $scheme;
    protected $username;
    protected $password;
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    /**
     * URI constructor
     *
     * @param string $uri
     * @throws InvalidUrl
     */
    public function __construct(string $uri)
    {
        $parts = parse_url($uri);
        if (false === $parts) {
            throw new InvalidUrl('Invalid URL');
        }
        $this->scheme = strtolower($parts['scheme'] ?? '');
        $this->username = $parts['user'] ?? '';
        $this->password = $parts['pass'] ?? '';
        $this->host = strtolower($parts['host'] ?? '');
        $this->port = $parts['port'] ?? null;
        $this->path = $parts['path'] ?? '';
        $this->query = $parts['query'] ?? '';
        $this->fragment = $parts['fragment'] ?? '';
    }

    /**
     * Retrieve the scheme component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Alias for $this->getScheme() :
     * Retrieve the scheme component of the URI.
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->getScheme();
    }

    /**
     * Retrieve the user name information component of the URI.
     *
     * User name or empty string
     * @return string
     */
    public function getUserName(): string
    {
        return $this->username;
    }

    /**
     * Retrieve the password information component of the URI.
     * Returns password or empty string
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo(): string
    {
        if (empty($this->getUserName())) {
            return '';
        }
        if (empty($this->getPassword())) {
            return $this->getUserName();
        }
        return $this->getUserName() . ':' . $this->getPassword();
    }

    /**
     * Retrieve the host component of the URI.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Retrieve the port component of the URI.
     *
     * @return null|int The URI port.
     * @7.1
     */
    public function getPort()/*: ?int*/
    {
        if (isset(static::PORTS[$this->getScheme()]) && $this->port === static::PORTS[$this->getScheme()]) {
            return null;
        }
        return $this->port;
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority(): string
    {
        $ret = '';
        if (!empty($this->getUserInfo())) {
            $ret .= $this->getUserInfo() . '@';
        }
        $ret .= $this->getHost();
        if (!empty($this->getPort())) {
            $ret .= ':' . $this->getPort();
        }
        return $ret;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Retrieve the query params array (or iterable object) from the URI.
     *
     * @7.1
     * @return iterable
     */
    public function getQueryParams()/*: iterable*/
    {
        parse_str($this->getQuery(), $res);
        return new Std($res);
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme = null)
    {
        $clone = clone $this;
        $clone->scheme = empty($scheme) ? '' : strtolower((string)$scheme);
        return $clone;
    }

    /**
     * Alias for $this->withScheme() :
     * Return an instance with the specified scheme.
     *
     * @param $protocol
     * @return static
     */
    public function withProtocol(string $protocol = null)
    {
        return $this->withScheme($protocol);
    }

    /**
     * Return an instance with the specified user name.
     *
     * @param string$userName
     * @return static
     */
    public function withUserName(string $userName)
    {
        $clone = clone $this;
        $clone->username = (string)$userName;
        return $clone;
    }

    /**
     * Return an instance with the specified password.
     *
     * @param string $password
     * @return static
     */
    public function withPassword(string $password = null)
    {
        $clone = clone $this;
        $clone->password = empty($password) ? '' : (string)$password;
        return $clone;
    }

    /**
     * Remove the password
     * Return an instance without the password component.
     *
     * @return static
     */
    public function withoutPassword()
    {
        return $this->withPassword();
    }

    /**
     * Return an instance with the specified user information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        if (empty($user)) {
            return $this->withoutUserInfo();
        }
        return $this->withUserName($user)->withPassword($password);
    }

    /**
     * Remove the user info
     * Return an instance without the user info component.
     *
     * @return static
     */
    public function withoutUserInfo()
    {
        $clone = clone $this;
        $clone->username = '';
        $clone->password = '';
        return $clone;
    }

    /**
     * Return an instance with the specified host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host = null)
    {
        if (empty($host)) {
            return $this->withoutHost();
        }
        $clone = clone $this;
        $clone->host = strtolower($host);
        return $clone;
    }

    /**
     * Remove the host
     * Return an instance without the host component.
     *
     * @return static A new instance without the host.
     */
    public function withoutHost()
    {
        $clone = clone $this;
        $clone->host = '';
        return $clone;
    }

    /**
     * Return an instance with the specified port.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port = null)
    {
        if (empty($port)) {
            return $this->withoutPort();
        }
        $clone = clone $this;
        $clone->port = (int)$port;
        return $clone;
    }

    /**
     * Remove the port
     * Return an instance without the port component.
     *
     * @return static A new instance without the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withoutPort()
    {
        $clone = clone $this;
        $clone->port = null;
        return $clone;
    }

    /**
     * Return an instance with the specified path.
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $clone = clone $this;
        $clone->path = (string)$path;
        return $clone;
    }

    /**
     * Return an instance with the specified query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        // TODO: Implement withQuery() method.
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    /**
     * Return an instance with the specified query parameter and its value.
     *
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function withQueryParam(string $name, $value)
    {
        // TODO: Implement withQueryParam() method.
    }

    /**
     * Remove the query parameter
     * Return an instance without the specified query parameter.
     *
     * @param string $name
     * @return static
     */
    public function withoutQueryParam(string $name)
    {
        // TODO: Implement withoutQueryParam() method.
    }

    /**
     * Return the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}