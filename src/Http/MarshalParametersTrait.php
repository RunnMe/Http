<?php

namespace Runn\Http;

use http\Exception\UnexpectedValueException;

trait MarshalParametersTrait
{

    /**
     * Marshal headers from $_SERVER
     *
     * @return array
     */
    private static function marshalHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            // Apache prefixes environment variables with REDIRECT_
            // if they are added by rewrite rules
            if (strpos($key, 'REDIRECT_') === 0) {
                $key = substr($key, 9);
                // We will not overwrite existing variables with the
                // prefixed versions, though
                if (array_key_exists($key, $_SERVER)) {
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
     * @param array $headers
     * @return Uri
     * @throws Exceptions\InvalidUri
     */
    private static function marshalUri(array $headers): Uri
    {
        $uri = new Uri('');
        // URI scheme
        $scheme = 'http';
        $https  = $_SERVER['HTTPS'] ?? false;
        if (($https && 'off' !== $https)
            || 'https' === self::getHeaderFromArray('x-forwarded-proto', $headers, false)
        ) {
            $scheme = 'https';
        }
        $uri = $uri->withScheme($scheme);
        // Set the host
        $accumulator = (object) ['host' => '', 'port' => null];
        self::marshalHostAndPortFromHeaders($accumulator, $_SERVER, $headers);
        $host = $accumulator->host;
        $port = $accumulator->port;
        if (! empty($host)) {
            $uri = $uri->withHost($host);
            if (! empty($port)) {
                $uri = $uri->withPort($port);
            }
        }
        // URI path
        $path = self::marshalRequestUri($_SERVER);
        $path = self::stripQueryString($path);
        // URI query
        $query = '';
        if (isset($_SERVER['QUERY_STRING'])) {
            $query = ltrim($_SERVER['QUERY_STRING'], '?');
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
     * Return HTTP protocol version (X.Y)
     *
     * @return string
     */
    private static function marshalProtocolVersion(): string
    {
        if (!isset($_SERVER['SERVER_PROTOCOL'])) {
            return '1.1';
        }

        if (!preg_match('#^(HTTP/)?(?P<version>[1-9]\d*(?:\.\d)?)$#', $_SERVER['SERVER_PROTOCOL'], $matches)) {
            throw new UnexpectedValueException(sprintf(
                'Unrecognized protocol version (%s)',
                $_SERVER['SERVER_PROTOCOL']
            ));
        }

        return $matches['version'];
    }

    /**
     * Search for a header value.
     * Does a case-insensitive search for a matching header.
     * If found, it is returned as a string, using comma concatenation.
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
}
