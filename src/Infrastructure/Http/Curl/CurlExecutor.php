<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Curl;

use App\Shared\Exception\CurlException;

/**
 * CurlHttpClient
 *
 * @package App\Infrastructure\Http\Curl;
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class CurlExecutor
{
    private int $statusCode;
    private int $headerSize;

    /**
     * execute
     *
     * @param array $options
     * @return string
     * @throws CurlException     */
    public function execute(array $options): string
    {
        $ch = curl_init();
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

        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        curl_close($ch);

        return $raw;
    }

    /**
     * getStatusCode
     *
     * @return integer
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * getHeaderSize
     *
     * @return integer
     */
    public function getHeaderSize(): int
    {
        return $this->headerSize;
    }
}
