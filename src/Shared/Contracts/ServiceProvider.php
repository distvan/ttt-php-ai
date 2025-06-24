<?php

declare(strict_types=1);

namespace App\Shared\Contracts;

use Psr\Container\ContainerInterface;

/**
 * ServiceProvider Interface
 */
interface ServiceProvider
{
    /**
     * Register
     *
     * @param Container $container
     */
    public function register(ContainerInterface $container): void;
}
