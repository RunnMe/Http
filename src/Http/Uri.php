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
     * URI constructor.
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
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Alias for getScheme()
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->getScheme();
    }

    /**
     * User name or empty string
     * @return string
     */
    public function getUserName(): string
    {
        return $this->username;
    }

    /**
     * Password or empty string
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
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
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
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
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @7.1
     * @return Std
     */
    public function getQueryParams()/*: iterable*/
    {
        parse_str($this->getQuery(), $res);
        return new Std($res);
    }

    /**
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
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified scheme.
     *
     * Implementations MUST support the schemes "http" and "https" case
     * insensitively, and MAY accommodate other schemes if required.
     *
     * An empty scheme is equivalent to removing the scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        // TODO: Implement withScheme() method.
    }

    /**
     * Return an instance with the specified user information.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        // TODO: Implement withUserInfo() method.
    }

    /**
     * Return an instance with the specified host.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        // TODO: Implement withHost() method.
    }

    /**
     * Return an instance with the specified port.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified port.
     *
     * Implementations MUST raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        // TODO: Implement withPort() method.
    }

    /**
     * Return an instance with the specified query string.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified query string.
     *
     * Users can provide both encoded and decoded query characters.
     * Implementations ensure the correct encoding as outlined in getQuery().
     *
     * An empty query string value is equivalent to removing the query string.
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
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified URI fragment.
     *
     * Users can provide both encoded and decoded fragment characters.
     * Implementations ensure the correct encoding as outlined in getFragment().
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        // TODO: Implement withFragment() method.
    }

    /**
     * @param $protocol
     * @return static
     */
    public function withProtocol(string $protocol)
    {
        // TODO: Implement withProtocol() method.
    }

    /**
     * @param string $password
     * @return static
     */
    public function withPassword(string $password)
    {
        // TODO: Implement withPassword() method.
    }

    /**
     * Return an instance with the specified path.
     *
     * This method MUST retain the state of the current instance, and return
     * an instance that contains the specified path.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * If the path is intended to be domain-relative rather than path relative then
     * it must begin with a slash ("/"). Paths not starting with a slash ("/")
     * are assumed to be relative to some base path known to the application or
     * consumer.
     *
     * Users can provide both encoded and decoded path characters.
     * Implementations ensure the correct encoding as outlined in getPath().
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        // TODO: Implement withPath() method.
    }

    /**
     * @return static
     */
    public function withoutUserInfo()
    {
        // TODO: Implement withoutUserInfo() method.
    }

    /**
     * @param string $userName
     * @return static
     */
    public function withUserName(string $userName)
    {
        // TODO: Implement withUserName() method.
    }

    /**
     * @return static
     */
    public function withoutPassword()
    {
        // TODO: Implement withoutPassword() method.
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function withQueryParam(string $name, $value)
    {
        // TODO: Implement withQueryParam() method.
    }

    /**
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
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}