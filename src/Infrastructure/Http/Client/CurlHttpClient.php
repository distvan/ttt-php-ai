<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Client;

use App\Shared\CurlException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * CurlHttpClient
 *
 * @package App\Infrastructure\Http\Client;
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class CurlHttpClient implements ClientInterface
{
    private StreamFactoryInterface $streamFactory;
    private ResponseFactoryInterface $responseFactory;

    /**
     * Constructor
     *
     * @param StreamFactoryInterface $streamFactory
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * sendRequest
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $ch = curl_init();

        $headers = [];
        foreach ($request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $headers[] = $name . ': ' . $value;
            }
        }

        $options = [
            CURLOPT_URL => (string) $request->getUri(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $request->getMethod(),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_HEADER => true,
        ];

        if ($request->getBody()->getSize()) {
            $request->getBody()->rewind();
            $options[CURLOPT_POSTFIELDS] = $request->getBody()->getContents();
        }

        curl_setopt_array($ch, $options);

        $raw = curl_exec($ch);

        if ($raw === false) {
            $error = curl_error($ch);
            $info = curl_getinfo($ch);
            curl_close($ch);
            $errorMessage = $error ?: 'Unknown cURL error';
            $errorMessage .= "\nRequest URL: " . ($info['url'] ?? 'N/A');
            $errorMessage .= "\nHTTP Code: " . ($info['http_code'] ?? 'N/A');
            
            throw new CurlException("cURL error: $errorMessage");
        }
        
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $rawHeaders = substr($raw, 0, $headerSize);
        $body = substr($raw, $headerSize);

        $response = $this->responseFactory->createResponse($status);
        foreach (explode("\r\n", $rawHeaders) as $headerLine) {
            if (strpos($headerLine, ':') !== false) {
                [$name, $value] = explode(':', $headerLine, 2);
                $response = $response->withAddedHeader(trim($name), trim($value));
            }
        }

        $response = $response->withBody($this->streamFactory->createStream($body));
        return $response;
    }
}
