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
    public function save(string $key, mixed $value): void;
    public function load(string $key): mixed;
    public function delete(string $key): void;
    public function exists(string $key): bool;
    public function clear(): void;
}
