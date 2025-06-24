<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * JsonResponseFactory
 *
 * @package App\Shared\Http
 */
class JsonResponseFactory
{
    /**
    * Create
    *
    * @param array $data
    * @param int $status
    * @param int $options
    */
    public static function create(array $data, int $status = 200, int $options = 0): ResponseInterface
    {
        $json = json_encode($data, $options);

        if ($json === false) {
            $json = json_encode([
                'error' => true,
                'message' => 'JSON encoding failed',
            ]);
            $status = 500;
        }

        return new Response(
            $status,
            ['Content-Type' => 'application/json'],
            $json
        );
    }
}
