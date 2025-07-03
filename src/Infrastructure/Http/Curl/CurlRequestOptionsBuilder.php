<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Curl;

use Psr\Http\Message\RequestInterface;

/**
 * CurlHttpClient
 *
 * @package App\Infrastructure\Http\Curl;
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class CurlRequestOptionsBuilder
{
    /**
     * build
     *
     * @param RequestInterface $request
     * @return array
     */
    public function build(RequestInterface $request): array
    {
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
            CURLOPT_HEADER => true
        ];

        if ($request->getBody()->getSize()) {
            $request->getBody()->rewind();
            $options[CURLOPT_POSTFIELDS] = $request->getBody()->getContents();
        }

        return $options;
    }
}
