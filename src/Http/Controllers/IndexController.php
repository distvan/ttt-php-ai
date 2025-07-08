<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Shared\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * IndexController
 *
 * @package App\Http\Controllers
 */
class IndexController
{
    /**
     * Constructor
     *
     * @param View $view
     */
    public function __construct(
        private View $view
    ) {
    }

    /**
     * Init board
     *
     * __invoke
     *
     * @param ServerRequestInterface $request
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $apiUrl = $_ENV['APPLICATION_API_URL'] ?? "http://localhost:8080";
        return $this->view->render('index', ['apiUrl' => $apiUrl, 'title' => 'Tic-Tac-Toe'], 'main', '');
    }
}
