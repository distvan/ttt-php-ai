<?php

declare(strict_types=1);

namespace App\Storage;

use App\Domain\Contracts\Storage;
use Psr\Log\LoggerInterface;

/**
 * SessionStorage
 *
 * @package App\Storage
 */
class SessionStorage implements Storage
{
    /**
     * @var string $namespace
     */
    private string $namespace;

    private $logger;

    /**
     * Constructor
     *
     * @param string $namespace
     */
    public function __construct(LoggerInterface $logger, string $namespace = 'app_state')
    {
        $this->ensureSessionStarted();
        $this->namespace = $namespace;
        $this->logger = $logger;

        if (!isset($_SESSION[$this->namespace])) {
            $_SESSION[$this->namespace] = [];
        }
    }

    /**
     * Save
     *
     * @param string $key
     * @param mixed $value
     */
    public function save(string $key, mixed $value): void
    {
        $_SESSION[$this->namespace][$key] = $value;
        if ($_ENV['LOG_LEVEL'] == 'debug') {
            $this->logger->debug('SessionStorage::save', ['key' => $key, 'value' => $value]);
        }
    }

    /**
     * Load
     *
     * @param string $key
     */
    public function load(string $key): mixed
    {
        return $_SESSION[$this->namespace][$key] ?? null;
    }

    /**
     * Delete
     *
     * @param string $key
     */
    public function delete(string $key): void
    {
        unset($_SESSION[$this->namespace][$key]);
    }

    /**
     * Exists
     *
     * @param string $key
     */
    public function exists(string $key): bool
    {
        return array_key_exists($key,$_SESSION[$this->namespace]);
    }

    /**
     * Clear
     *
     */
    public function clear(): void
    {
        $_SESSION[$this->namespace] = [];
    }

    /**
     * EnsureSessionStarted
     * 
     */
    private function ensureSessionStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
