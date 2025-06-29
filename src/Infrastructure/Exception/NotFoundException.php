<?php

declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * NotFoundException
 *
 * @package App\Infrastructure\Exception
 */
class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
