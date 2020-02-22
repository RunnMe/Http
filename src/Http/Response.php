<?php

namespace Runn\Http;

/**
 * Base HTTP response class
 *
 * Class Response
 * @package Runn\Http
 */
class Response extends \Slim\Psr7\Response implements ResponseInterface
{

    public const CHUNK_SIZE = 1024 * 4;

    /**
     * Send the response to the client
     */
    public function send()
    {
        // Send response
        if (!headers_sent()) {
            // Headers
            foreach ($this->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }

            header(sprintf(
                'HTTP/%s %s %s',
                $this->getProtocolVersion(),
                $this->getStatusCode(),
                $this->getReasonPhrase()
            ));
        }

        // Body
        if (!$this->isEmptyResponse($this)) {
            $body = $this->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            $contentLength = $this->getHeaderLine('Content-Length');
            if (!$contentLength) {
                $contentLength = $body->getSize();
            }

            if (isset($contentLength)) {
                $amountToRead = $contentLength;
                while ($amountToRead > 0 && !$body->eof()) {
                    $data = $body->read(min(self::CHUNK_SIZE, $amountToRead));
                    echo $data;

                    $amountToRead -= strlen($data);

                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            } else {
                while (!$body->eof()) {
                    echo $body->read(self::CHUNK_SIZE);
                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Helper method, which returns true if the provided response must not output a body and false
     * if the response could have a body.
     *
     * @see https://tools.ietf.org/html/rfc7231
     *
     * @param ResponseInterface $response
     * @return bool
     */
    protected function isEmptyResponse(ResponseInterface $response)
    {
        if (method_exists($response, 'isEmpty')) {
            return $response->isEmpty();
        }

        return in_array($response->getStatusCode(), [204, 205, 304]);
    }

}
