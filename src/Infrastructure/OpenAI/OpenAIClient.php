<?php

declare(strict_types=1);

namespace App\Infrastructure\OpenAI;

use App\Domain\Contracts\OpenAIClient as OpenAIClientInterface;
use App\Infrastructure\Exception\OpenAIException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * OpenAIClient
 *
 * @package App\Infrastructure\OpenAI
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class OpenAIClient implements OpenAIClientInterface
{
    /**
     * Construct
     *
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param StreamFactoryInterface $streamFactory
     * @param string $apiKey
     * @param string $baseUrl default value: https://openrouter.ai/api/v1/
     */
    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private string $apiKey,
        private string $baseUrl = 'https://openrouter.ai/api/v1/'
    ) {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    /**
     * chat send prompt message to the AI client
     *
     * @param array $messages
     * @param string $model
     * @param float $temperature default value: 0.7
     * @return string
     */
    public function chat(
        array $messages,
        string $model = 'deepseek/deepseek-chat-v3-0324:free',
        float $temperature = 0.7
    ): string {
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
        ];

        $request = $this->requestFactory->createRequest('POST', $this->baseUrl . 'chat/completions')
            ->withHeader('Authorization', 'Bearer ' . $this->apiKey)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode($payload)));

        $response = $this->httpClient->sendRequest($request);

        return $this->parseResponse($response);
    }

    /**
     * parseResponse
     *
     * @param ResponseInterface $response
     * @return string
     */
    private function parseResponse(ResponseInterface $response): string
    {
        $status = $response->getStatusCode();
        $data = json_decode((string) $response->getBody(), true);

        if ($status >= 400 || !is_array($data)) {
            throw new OpenAIException("OpenAI API error ($status): " . json_encode($data));
        }

        return $data['choices'][0]['message']['content'] ?? '';
    }
}
