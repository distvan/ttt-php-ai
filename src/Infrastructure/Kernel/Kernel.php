<?php

declare(strict_types=1);

namespace App\Infrastructure\Kernel;

use App\Infrastructure\Container;
use App\Shared\KernelException;
use App\Shared\Contracts\ServiceProvider;

class Kernel
{
    public function __construct(
        protected Container $container,
        protected array $providers = []
    ){}
    
    public function getContainer(): Container {
        return $this->container;
    }
    
    public function registerProviders(array $providerClasses): void {
        foreach ($providerClasses as $providerClass) {
            $provider = new $providerClass();
            if (!$provider instanceof ServiceProvider) {
                throw new KernelException("$providerClass must implement ServiceProviderInterface");
            }

            $provider->register($this->container);
            $this->providers[] = $provider;
        }
    }
}
