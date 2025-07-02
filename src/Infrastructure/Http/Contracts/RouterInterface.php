<?php

namespace App\Infrastructure\Http\Contracts;

use Psr\Http\Message\ServerRequestInterface;

/**
 * RouterInterface
 *
 * @package App\Infrastructure\Http\Contracts
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
interface RouterInterface
{
    /**
     * Add
     *
     * @param string $method
     * @param string $path
     * @param callable $handler
     * @return void
     */
    public function add(string $method, string $path, callable $handler): void;
    /**
     * Match
     *
     * @param ServerRequestInterface $request
     * @return callable|null
     */
    public function match(ServerRequestInterface $request): ?callable;
}
