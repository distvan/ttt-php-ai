<?php

namespace App\Application\Contracts;

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
     * @param array $board 3x3 array representing the board
     * @param string ai model name
     * @return array ['row' => int, 'col' => int]
     */
    public function suggestMove(array $board, string $model): array;
}
