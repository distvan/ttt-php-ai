<?php

declare(strict_types=1);

namespace App\Infrastructure\Kernel;

use App\Infrastructure\Exception\KernelException;
use App\Shared\Contracts\ServiceProvider;
use Psr\Container\ContainerInterface;

/**
 * Kernel
 *
 * @package App\Infrastructure\Kernel
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class Kernel
{
    public function __construct(
        protected ContainerInterface $container,
        protected array $providers = []
    ) {
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function registerProviders(array $providerClasses): void
    {
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
