<?php

namespace Runn\Http;

use GuzzleHttp\Psr7\Stream;

/**
 * Class Request
 * @package Runn\Http
 */
class ServerRequest extends \GuzzleHttp\Psr7\ServerRequest implements ServerRequestInterface
{
    use MarshalParametersTrait;

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getServerParams(): array
    {
        return parent::getServerParams();
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getCookieParams(): array
    {
        return parent::getCookieParams();
    }

    /**
     * Return an instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * @return static
     *
     * @codeCoverageIgnore
     */
    public function withCookieParams(array $cookies): ServerRequestInterface
    {
        return parent::withCookieParams($cookies);
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getQueryParams(): array
    {
        return parent::getQueryParams();
    }

    /**
     * Return an instance with the specified query string arguments.
     *
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     *
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     * @return static
     *
     * @codeCoverageIgnore
     */
    public function withQueryParams(array $query): ServerRequestInterface
    {
        return parent::withQueryParams($query);
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     *
     * @codeCoverageIgnore
     */
    public function getUploadedFiles(): array
    {
        return parent::getUploadedFiles();
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * @return static
     * @throws \InvalidArgumentException if an invalid structure is provided.
     *
     * @codeCoverageIgnore
     */
    public function withUploadedFiles(array $uploadedFiles): ServerRequestInterface
    {
        return parent::withUploadedFiles($uploadedFiles);
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     *
     * @codeCoverageIgnore
     * @7.1
     */
    public function getParsedBody()/*: ?array*/
    {
        return parent::getParsedBody();
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return static
     * @throws \InvalidArgumentException if an unsupported argument type is
     *     provided.
     *
     * @codeCoverageIgnore
     */
    public function withParsedBody($data): ServerRequestInterface
    {
        return parent::withParsedBody($data);
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     *
     * @codeCoverageIgnore
     */
    public function getAttributes(): array
    {
        return parent::getAttributes();
    }

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return static
     *
     * @codeCoverageIgnore
     */
    public function withAttribute($name, $value): ServerRequestInterface
    {
        return parent::withAttribute($name, $value);
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @return static
     *
     * @codeCoverageIgnore
     */
    public function withoutAttribute($name): ServerRequestInterface
    {
        return parent::withoutAttribute($name);
    }

    /**
     * Creates object from $_SERVER and php://input
     * @param array|null $server
     * @param string $stream
     * @return ServerRequestInterface
     * @throws Exceptions\InvalidUri
     * @throws Exceptions\UnexpectedValueException
     */
    public static function constructFromGlobals(
        array $server = null,
        string $stream = self::PHP_INPUT
    ): ServerRequestInterface
    {
        $server = self::normalizeServer($server ?? $_SERVER);
        $headers = self::marshalHeaders($server);

        $method = $server['REQUEST_METHOD'] ?? 'GET';
        $uri = static::marshalUriFromServer($server, $headers);
        $body = new Stream(fopen($stream, 'rb'));
        $version = self::marshalProtocolVersion($server);

        return new static($method, $uri, $headers, $body, $version, $server);
    }

    public function __construct(
        string $method,
        $uri,
        array $headers = [],
        $body = null,
        string $version = '1.1',
        array $serverParams = []
    ) {
        parent::__construct($method, $uri, $headers, $body, $version, $serverParams);
    }
}
