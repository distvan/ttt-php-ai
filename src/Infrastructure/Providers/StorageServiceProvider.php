<?php

declare(strict_types=1);

namespace App\Infrastructure\Providers;

use App\Domain\Contracts\Storage as StorageInterface;
use App\Shared\Contracts\ServiceProvider;
use App\Storage\SessionStorage;
use Psr\Container\ContainerInterface;
use App\Shared\Logging\LoggerFactory;

class StorageServiceProvider implements ServiceProvider
{
    public function register(ContainerInterface $container): void
    {
        if (method_exists($container, 'bind')) {
            $container->bind(
                StorageInterface::class,
                function () {
                    return new SessionStorage(LoggerFactory::create());
                }
            );
        }
    }
}
