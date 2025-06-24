<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Application\Contracts\AIAssistant as AIAssistantInterface;
use App\Application\Services\AIAssistant as AIAssistantService;
use App\Infrastructure\Http\Client\CurlHttpClient;
use App\Infrastructure\OpenAI\OpenAIClient;
use App\Shared\Contracts\ServiceProvider;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;

class AIAssistantServiceProvider implements ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        if (method_exists($container, 'bind')) {
            $container->bind(
                AIAssistantInterface::class,
                function () {
                    $apiKey = !empty($_ENV["OPENAI_API_KEY"]) ? $_ENV["OPENAI_API_KEY"] : "";
                    $apiUrl = !empty($_ENV["OPENAI_API_URL"]) ? $_ENV["OPENAI_API_URL"] : "";
                    $factory = new Psr17Factory();
                    $httpClient = new CurlHttpClient($factory, $factory);
                    $dependency = new OpenAIClient($httpClient, $factory, $factory, $apiKey, $apiUrl);
                    return new AIAssistantService($dependency);
                }
            );
        }
    }
}
