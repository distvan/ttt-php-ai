<?php

namespace App\Infrastructure;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface IRouter
 *
 * @package App\Infrastructure
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
interface IRouter
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
