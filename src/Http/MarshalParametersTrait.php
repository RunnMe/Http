<?php

namespace Runn\Http;

use Runn\Http\Exceptions\UnexpectedValueException;
use stdClass;

trait MarshalParametersTrait
{
    /**
     * Function to use to get apache request headers; present only to simplify mocking.
     *
     * @var callable
     */
    private static $apacheRequestHeaders = 'apache_request_headers';

    /**
     * Access a value in an array, returning a default value if not found
     *
     * Will also do a case-insensitive search if a case sensitive search fails.
     *
     * @param string $key
     * @param array $values
     * @param mixed $default
     * @return mixed
     */
    private static function get($key, array $values, $default = null)
    {
        if (array_key_exists($key, $values)) {
            return $values[$key];
        }
        return $default;
    }

    /**
     * Search for a header value.
     *
     * Does a case-insensitive search for a matching header.
     *
     * If found, it is returned as a string, using comma concatenation.
     *
     * If not, the $default is returned.
     *
     * @param string $header
     * @param array $headers
     * @param mixed $default
     * @return string
     */
    private static function getHeaderFromArray($header, array $headers, $default = null): string
    {
        $header  = strtolower($header);
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (array_key_exists($header, $headers)) {
            $value = \is_array($headers[$header]) ? implode(', ', $headers[$header]) : $headers[$header];
            return $value;
        }
        return $default;
    }

    /**
     * Marshal the $_SERVER array
     *
     * Pre-processes and returns the $_SERVER superglobal.
     *
     * @param array $server
     * @return array
     */
    private static function normalizeServer(array $server): array
    {
        // This seems to be the only way to get the Authorization header on Apache
        $apacheRequestHeaders = self::$apacheRequestHeaders;
        if (isset($server['HTTP_AUTHORIZATION'])
            || ! \is_callable($apacheRequestHeaders)
        ) {
            return $server;
        }
        $apacheRequestHeaders = $apacheRequestHeaders();
        if (isset($apacheRequestHeaders['Authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['Authorization'];
            return $server;
        }
        if (isset($apacheRequestHeaders['authorization'])) {
            $server['HTTP_AUTHORIZATION'] = $apacheRequestHeaders['authorization'];
            return $server;
        }
        return $server;
    }

    /**
     * Marshal headers from $_SERVER
     *
     * @param array $server
     * @return array
     */
    private static function marshalHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            // Apache prefixes environment variables with REDIRECT_
            // if they are added by rewrite rules
            if (strpos($key, 'REDIRECT_') === 0) {
                $key = substr($key, 9);
                // We will not overwrite existing variables with the
                // prefixed versions, though
                if (array_key_exists($key, $server)) {
                    continue;
                }
            }
            if ($value && strpos($key, 'HTTP_') === 0) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;
                continue;
            }
            if ($value && strpos($key, 'CONTENT_') === 0) {
                $name = 'content-' . strtolower(substr($key, 8));
                $headers[$name] = $value;
                continue;
            }
        }
        return $headers;
    }

    /**
     * Marshal the URI from the $_SERVER array and headers
     *
     * @param array $server
     * @param array $headers
     * @return Uri
     * @throws Exceptions\InvalidUri
     */
    private static function marshalUriFromServer(array $server, array $headers): Uri
    {
        $uri = new Uri('');
        // URI scheme
        $scheme = 'http';
        $https  = self::get('HTTPS', $server);
        if (($https && 'off' !== $https)
            || self::getHeaderFromArray('x-forwarded-proto', $headers, false) === 'https'
        ) {
            $scheme = 'https';
        }
        $uri = $uri->withScheme($scheme);
        // Set the host
        $accumulator = (object) ['host' => '', 'port' => null];
        self::marshalHostAndPortFromHeaders($accumulator, $server, $headers);
        $host = $accumulator->host;
        $port = $accumulator->port;
        if (! empty($host)) {
            $uri = $uri->withHost($host);
            if (! empty($port)) {
                $uri = $uri->withPort($port);
            }
        }
        // URI path
        $path = self::marshalRequestUri($server);
        $path = self::stripQueryString($path);
        // URI query
        $query = '';
        if (isset($server['QUERY_STRING'])) {
            $query = ltrim($server['QUERY_STRING'], '?');
        }
        // URI fragment
        $fragment = '';
        if (strpos($path, '#') !== false) {
            list($path, $fragment) = explode('#', $path, 2);
        }
        return $uri
            ->withPath($path)
            ->withFragment($fragment)
            ->withQuery($query);
    }

    /**
     * Marshal the host and port from HTTP headers and/or the PHP environment
     *
     * @param stdClass $accumulator
     * @param array $server
     * @param array $headers
     */
    private static function marshalHostAndPortFromHeaders(stdClass $accumulator, array $server, array $headers)
    {
        if (self::getHeaderFromArray('host', $headers, false)) {
            self::marshalHostAndPortFromHeader($accumulator, self::getHeaderFromArray('host', $headers));
            return;
        }
        if (! isset($server['SERVER_NAME'])) {
            return;
        }
        $accumulator->host = $server['SERVER_NAME'];
        if (isset($server['SERVER_PORT'])) {
            $accumulator->port = (int) $server['SERVER_PORT'];
        }
        if (! isset($server['SERVER_ADDR']) || ! preg_match('/^\[[0-9a-fA-F\:]+\]$/', $accumulator->host)) {
            return;
        }
        // Misinterpreted IPv6-Address
        // Reported for Safari on Windows
        self::marshalIpv6HostAndPort($accumulator, $server);
    }
    /**
     * Detect the base URI for the request
     *
     * Looks at a variety of criteria in order to attempt to autodetect a base
     * URI, including rewrite URIs, proxy URIs, etc.
     *
     * From ZF2's Zend\Http\PhpEnvironment\Request class
     * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
     * @license   http://framework.zend.com/license/new-bsd New BSD License
     *
     * @param array $server
     * @return string
     */
    private static function marshalRequestUri(array $server): string
    {
        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = self::get('IIS_WasUrlRewritten', $server);
        $unencodedUrl    = self::get('UNENCODED_URL', $server, '');
        if ('1' === $iisUrlRewritten && ! empty($unencodedUrl)) {
            return $unencodedUrl;
        }
        $requestUri = self::get('REQUEST_URI', $server);
        // Check this first so IIS will catch.
        $httpXRewriteUrl = self::get('HTTP_X_REWRITE_URL', $server);
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }
        // Check for IIS 7.0 or later with ISAPI_Rewrite
        $httpXOriginalUrl = self::get('HTTP_X_ORIGINAL_URL', $server);
        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }
        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }
        $origPathInfo = self::get('ORIG_PATH_INFO', $server);
        if (empty($origPathInfo)) {
            return '/';
        }
        return $origPathInfo;
    }

    /**
     * Strip the query string from a path
     *
     * @param mixed $path
     * @return string
     */
    private static function stripQueryString($path): string
    {
        if (($qpos = strpos($path, '?')) !== false) {
            return substr($path, 0, $qpos);
        }
        return $path;
    }

    /**
     * Marshal the host and port from the request header
     *
     * @param stdClass $accumulator
     * @param string|array $host
     * @return void
     */
    private static function marshalHostAndPortFromHeader(stdClass $accumulator, $host)
    {
        if (\is_array($host)) {
            $host = implode(', ', $host);
        }
        $accumulator->host = $host;
        $accumulator->port = null;
        // works for regname, IPv4 & IPv6
        if (preg_match('|\:(\d+)$|', $accumulator->host, $matches)) {
            $accumulator->host = substr($accumulator->host, 0, -1 * (\strlen($matches[1]) + 1));
            $accumulator->port = (int) $matches[1];
        }
    }

    /**
     * Marshal host/port from misinterpreted IPv6 address
     *
     * @param stdClass $accumulator
     * @param array $server
     */
    private static function marshalIpv6HostAndPort(stdClass $accumulator, array $server)
    {
        $accumulator->host = '[' . $server['SERVER_ADDR'] . ']';
        $accumulator->port = $accumulator->port ?: 80;
        if ($accumulator->port . ']' === substr($accumulator->host, strrpos($accumulator->host, ':') + 1)) {
            // The last digit of the IPv6-Address has been taken as port
            // Unset the port so the default port can be used
            $accumulator->port = null;
        }
    }

    /**
     * Return HTTP protocol version (X.Y)
     *
     * @param array $server
     * @return string
     * @throws UnexpectedValueException
     */
    private static function marshalProtocolVersion(array $server): string
    {
        if (! isset($server['SERVER_PROTOCOL'])) {
            return '1.1';
        }
        if (! preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $server['SERVER_PROTOCOL'], $matches)) {
            throw new UnexpectedValueException(sprintf(
                'Unrecognized protocol version (%s)',
                $server['SERVER_PROTOCOL']
            ));
        }
        return $matches['version'];
    }
}
