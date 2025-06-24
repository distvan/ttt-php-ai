<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Application class
 *
 * @package App\Application
 */
class Application
{
    /**
     * Constructor
     *
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        private Dispatcher $dispatcher
    ) {
    }

    /**
     * Run
     *
     * @param ServerRequestInterface $request
     */
    public function run(ServerRequestInterface $request): void
    {
        $this->emit($this->dispatcher->handle($request));
    }

    /**
     * emit
     *
     * @param ResponseInterface $response
     */
    private function emit(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name:$value", false);
            }
        }

        echo $response->getBody();
    }
}
