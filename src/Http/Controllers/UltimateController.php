<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Shared\View;
use App\Domain\Game\Board;
use App\Domain\Contracts\Storage;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * UltimateController
 *
 * @package App\Http\Controllers
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class UltimateController
{
    /**
     * Constructor
     *
     * @param View $view
     * @param Board $board
     * @param Storage $storage
     */
    public function __construct(
        private View $view,
        private Board $board,
        private Storage $storage
    ) {
    }

    /**
     * __invoke
     * Initialize the Ultimate structure
     * There are nine boards plus one just for store the final result.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $apiUrl = $_ENV['APPLICATION_API_URL'] ?? "http://localhost:8080";
        return $this->view->render(
            'index',
            ['apiUrl' => $apiUrl, 'title' => 'Tic-Tac-Toe Ultimate'],
            'main_ultimate',
            ''
        );
    }
}
