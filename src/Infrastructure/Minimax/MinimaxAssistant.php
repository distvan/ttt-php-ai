<?php

declare(strict_types=1);

namespace App\Infrastructure\Minimax;

use App\Application\Contracts\AIAssistant;
use App\Domain\Game\Board;

/**
 * MinimaxAssistant class
 */
class MinimaxAssistant implements AIAssistant
{
    private const AI_PLAYER = 'O';
    private const HUMAN_PLAYER = 'X';

    /**
     * suggestMove
     * calculate the best movement using minimax algorithm and return the suggestion
     *
     * @param Board $board
     * @param string $model
     * @return array
     */
    public function suggestMove(Board $board, $model = ''): array
    {
        $bestScore = -INF;
        $bestMove = [];
        $currentBoard = $board->getBoard();

        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                if ($board->isValidMove($row, $col)) {
                    $currentBoard[$row][$col] = self::AI_PLAYER;
                    $board->setBoard($currentBoard);
                    //evaluate
                    $score = $this->minimax($board, 0, false);
                    //undo move
                    $currentBoard[$row][$col] = '';
                    $board->setBoard($currentBoard);
                    if ($row === 1 && $col === 1) {
                        $score += 0.1;
                    }
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMove = ['row' => $row, 'col' => $col];
                    }
                }
            }
        }

        return $bestMove;
    }

    /**
     * minimax
     *
     * @param Board $board
     * @param integer $depth
     * @param boolean $isMaximizing
     * @return integer
     */
    private function minimax(Board $board, int $depth, bool $isMaximizing): int
    {
        $winner = $board->getWinner();
        if ($winner === self::AI_PLAYER) {
            return 10 - $depth;
        }
        if ($winner === self::HUMAN_PLAYER) {
            return $depth - 10;
        }
        if ($board->isFull()) {
            return 0;
        }

        $bestScore = $isMaximizing ? -INF : INF;
        $currentBoard = $board->getBoard();

        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                if ($board->isValidMove($row, $col)) {
                    $currentBoard[$row][$col] = $isMaximizing ? self::AI_PLAYER : self::HUMAN_PLAYER;
                    $board->setBoard($currentBoard);
                    $score = $this->minimax($board, $depth + 1, !$isMaximizing);
                    $currentBoard[$row][$col] = '';
                    $board->setBoard($currentBoard);
                    $bestScore = $isMaximizing ? max($bestScore, $score) : min($bestScore, $score);
                }
            }
        }

        return $bestScore;
    }
}
