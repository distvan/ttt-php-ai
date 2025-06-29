<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

/**
 * Storage Interface
 *
 * @package App\Domain\Contracts
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
interface Storage
{
    /**
     * Save a value to the store using the key
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function save(string $key, mixed $value): void;
    /**
     * Load a value from storage using the key
     *
     * @param string $key
     * @return mixed
     */
    public function load(string $key): mixed;
    /**
     * Delete a value in the store
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
    /**
     * Checking a key in the store
     *
     * @param string $key
     * @return boolean
     */
    public function exists(string $key): bool;
    /**
     * Clear the storage
     *
     * @return void
     */
    public function clear(): void;
}
