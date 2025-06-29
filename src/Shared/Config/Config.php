<?php

declare(strict_types=1);

namespace App\Shared\Config;

use App\Shared\Exception\ConfigException;
use UnexpectedValueException;

/**
 * Config class
 *
 * @package App\Shared
 */
class Config
{
    protected string $basePath;

    /**
     * Construct
     *
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Get config file
     *
     * @param string $file
     */
    public function get(string $file): array
    {
        $path = "{$this->basePath}/{$file}.php";

        if (!file_exists($path)) {
            throw new ConfigException("Config file not found: $path");
        }

        /** @var array $config */
        $config = require_once $path;

        if (!is_array($config)) {
            throw new UnexpectedValueException("Config file must return an array: $path");
        }
        return $config;
    }
}
