<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Contracts\OpenAIClient as OpenAIClientInterface;
use App\Shared\Contracts\ServiceProvider;
use App\Infrastructure\OpenAI\OpenAIClient;
use Psr\Container\ContainerInterface;

class AIClientServiceProvider implements ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        if (method_exists($container, 'bind')) {
            $container->bind(
                OpenAIClientInterface::class,
                OpenAIClient::class
            );
        }
    }
}
