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

    // @7.1
    /*public */const PORTS = [
        'ftp' => 21,
        'ssh' => 22,
        'http' => 80,
        'nntp' => 119,
        'irc' => 194,
        'ldap' => 389,
        'https' => 443,
    ];

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
     * Alias for $this->getScheme() :
     * Retrieve the scheme component of the URI.
     *
     * @return string
     */
    public function getProtocol(): string;

    /**
     * Retrieve the user name information component of the URI.
     *
     * User name or empty string
     * @return string
     */
    public function getUserName(): string;

    /**
     * Retrieve the password information component of the URI.
     * Returns password or empty string
     *
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
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost(): string;

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     * @7.1
     */
    public function getPort()/*: ?int*/;

    /**
     * Retrieve the port component of the URI.
     *
     * If no port is present, but a scheme is present, this method returns
     * the standard port for that scheme.
     *
     * @return null|int The URI port.
     * @7.1
     */
    public function getPortNumber()/*: ?int*/;

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority(): string;

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath(): string;

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery(): string;

    /**
     * Retrieve the query params array (or iterable object) from the URI.
     *
     * @7.1
     * @return iterable
     */
    public function getQueryParams()/*: iterable*/;

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment(): string;

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
    public function withScheme($scheme = null);

    /**
     * Alias for $this->withScheme() :
     * Return an instance with the specified scheme.
     *
     * @param $protocol
     * @return static
     */
    public function withProtocol(string $protocol = null);

    /**
     * Return an instance with the specified user name.
     *
     * @param string$userName
     * @return static
     */
    public function withUserName(string $userName);

    /**
     * Return an instance with the specified password.
     *
     * @param string $password
     * @return static
     */
    public function withPassword(string $password = null);

    /**
     * Remove the password
     * Return an instance without the password component.
     *
     * @return static
     */
    public function withoutPassword();

    /**
     * Remove the user info
     * Return an instance without the user info component.
     *
     * @return static
     */
    public function withoutUserInfo();

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
    public function withHost($host = null);

    /**
     * Remove the host
     * Return an instance without the host component.
     *
     * @return static A new instance without the host.
     */
    public function withoutHost();

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
    public function withPort($port = null);

    /**
     * Remove the port
     * Return an instance without the port component.
     *
     * @return static A new instance without the port component.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withoutPort();

    /**
     * Return an instance with the specified query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query = null);

    /**
     * Remove the query string
     * Return an instance without the query string.
     *
     * @return static A new instance without the query string.
     */
    public function withoutQuery();

    /**
     * Return an instance with the specified query parameter and its value.
     *
     * @param string $name
     * @param string|null $value
     * @return static
     */
    public function withQueryParam(string $name, string $value = null);

    /**
     * Remove the query parameter
     * Return an instance without the specified query parameter.
     *
     * @param string $name
     * @return static
     */
    public function withoutQueryParam(string $name);

    /**
     * Return an instance with the specified URI fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment = null);

    /**
     * Remove the fragment
     * Return an instance without the fragment component.
     *
     * @return static A new instance without the fragment.
     */
    public function withoutFragment();

    /**
     * Return the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString(): string;
}
