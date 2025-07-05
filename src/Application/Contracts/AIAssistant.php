<?php

namespace App\Application\Contracts;

use App\Domain\Game\Board;

/**
 * AIAssistant Interface
 *
 * @package App\Application\Contracts
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
interface AIAssistant
{
    /**
     * Suggest the best move for Tic Tac toe given the board state
     *
     * @param Board $board 3x3 array representing the board
     * @param string ai model name
     * @return array ['row' => int, 'col' => int]
     */
    public function suggestMove(Board $board, string $model): array;
}
