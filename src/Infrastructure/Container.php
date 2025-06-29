<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Infrastructure\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use InvalidArgumentException;

/**
 * Container class
 *
 * @package App\Infrastructure
 */
class Container implements ContainerInterface
{
    protected array $bindings = [];
    protected array $instances = [];

    /**
     * Bind
     *
     * @param string $id
     * @param string $resolver
     */
    public function bind(string $id, string|callable $resolver): void
    {
        if (is_string($resolver)) {
            $resolver = function () use ($resolver) {
                return new $resolver();
            };
        }
        if (!is_callable($resolver)) {
            throw new InvalidArgumentException("Service definition must be callable.");
        }
        $this->bindings[$id] = $resolver;
    }

    /**
     * Get
     *
     * @param string $id
     */
    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFoundException("No entry found for $id");
        }

        if (!isset($this->instances[$id])) {
            $this->instances[$id] = $this->bindings[$id]($this);
        }

        return $this->instances[$id];
    }

    /**
     * Has
     *
     * @param string $id
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }
}
