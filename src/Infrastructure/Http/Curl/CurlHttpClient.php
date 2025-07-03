<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Curl;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * CurlHttpClient
 *
 * @package App\Infrastructure\Http\Curl;
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class CurlHttpClient implements ClientInterface
{
    /**
     * Constructor
     *
     * @param CurlRequestOptionsBuilder $optionsBuilder
     * @param CurlExecutor $executor
     * @param CurlResponseParser $responseParser
     */
    public function __construct(
        private CurlRequestOptionsBuilder $optionsBuilder,
        private CurlExecutor $executor,
        private CurlResponseParser $responseParser
    ) {
    }

    /**
     * sendRequest
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $options = $this->optionsBuilder->build($request);
        $raw = $this->executor->execute($options);
        return $this->responseParser->parse($raw);
    }
}
