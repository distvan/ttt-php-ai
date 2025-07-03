<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Curl;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * CurlHttpClient
 *
 * @package App\Infrastructure\Http\Curl
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class CurlResponseParser
{
    /**
     * Constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param StreamFactoryInterface $streamFactory
     * @param CurlExecutor $executor
     */
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private CurlExecutor $executor
    ){
    }

    /**
     * parse
     *
     * @param string $raw
     * @return ResponseInterface
     */
    public function parse(string $raw): ResponseInterface
    {
        $headerSize = $this->executor->getHeaderSize();
        $statusCode = $this->executor->getStatusCode();

        $rawHeaders = substr($raw, 0, $headerSize);
        $body = substr($raw, $headerSize);

        $response = $this->responseFactory->createResponse($statusCode);
        foreach (explode("\r\n", $rawHeaders) as $headerLine) {
            if (strpos($headerLine, ':') !== false) {
                [$name, $value] = explode(':', $headerLine, 2);
                $response = $response->withAddedHeader(trim($name), trim($value));
            }
        }

        return $response->withBody($this->streamFactory->createStream($body));
    }
}
