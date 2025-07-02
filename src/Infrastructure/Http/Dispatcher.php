<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use App\Infrastructure\Http\Contracts\RouterInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Dispatcher class
 *
 * @package App\Infrastructure\Http
 */
class Dispatcher
{
    private const NOT_FOUND = 404;

    /**
     * Constructor
     *
     * @param RouterInterface $router
     */
    public function __construct(
        private RouterInterface $router
    ) {
    }

    /**
     * handle
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $handler = $this->router->match($request);

        if ($handler === null) {
            return new Response(self::NOT_FOUND, [], 'Not Found');
        }

        return $handler($request);
    }
}
