<?php

declare(strict_types=1);

namespace App\Domain\Game;

use InvalidArgumentException;

/**
 * Board class
 *
 * @package App\Domain
 */
class Board
{
    private array $board = [];
    private int $size = 3;

    public function createEmptyBoard(int $size=3): void
    {
        for ($i=0; $i < $size; $i++) {
            $this->board[$i] = array_fill(0, $size, '');
        }
        $this->size = $size;
    }

    /**
     * GetBoard
     */
    public function getBoard(): array
    {
        return $this->board;
    }

    /**
     * SetBoard
     *
     * @param array $board
     */
    public function setBoard(array $board):void
    {
        $this->validateBoard($board);
        $this->board = $board;
        $this->size = count($board);
    }

    /**
     * ValidateBoard
     *
     * @param $board
     * @throws InvalidArgumentException
     */
    public function  validateBoard(array $board): void
    {
        $rowCount = count($board);

        if ($rowCount < 3) {
            throw new InvalidArgumentException("Board must be at least 3x3.");
        }

        foreach ($board as $row) {
            if (!is_array($row) || count($row) !== $rowCount) {
                throw new InvalidArgumentException("Board must be square (NxN).");
            }
        }
    }

    /**
     * GetWinner
     */
    public function getWinner(): ?string
    {
        $lines = array_merge(
            $this->board,
            $this->getColumns(),
            $this->getDiagonals()
        );

        foreach ($lines as $line) {
            if (!empty($line[0]) && $this->allEqual($line)) {
                return $line[0];
            }
        }

        return null;
    }

    /**
     * GetColumns
     */
    private function getColumns(): array
    {
        $columns = [];
        for ($col = 0; $col < $this->size; $col++) {
            $column = [];
            for ($row = 0; $row < $this->size; $row++) {
                $column[] = $this->board[$row][$col];
            }
            $columns[] = $column;
        }
        
        return $columns;
    }

    /**
     * GetDiagonals
     */
    private function getDiagonals(): array
    {
        $main = [];
        $anti = [];
        for ($i = 0; $i < $this->size; $i++) {
            $main[] = $this->board[$i][$i];
            $anti[] = $this->board[$i][$this->size - $i - 1];
        }

        return [$main, $anti];
    }

    /**
     * AllEqual
     *
     * @param array $cells
     */
    private function allEqual(array $cells): bool
    {
        return count(array_unique($cells)) === 1;
    }

    /**
     * HasNoWinnerButBoardIsFull
     *
     */
    public function hasNoWinnerButBoardIsFull(): bool
    {
        return $this->getWinner() === null && $this->isFull();
    }

    /**
     * IsFull
     */
    public function isFull(): bool
    {
        foreach ($this->board as $row) {
            foreach ($row as $cell) {
                if (empty($cell)) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * IsValidMove
     *
     * @param int $row
     * @param int $col
     */
    private function isValidMove(int $row, int $col): bool
    {
        return isset($this->board[$row][$col]) && empty($this->board[$row][$col]);
    }

    /**
     * ApplyMove
     *
     * @param int $row
     * @param int $col
     * @param string $player
     * @throws InvalidArgumentException
     */
    public function applyMove(int $row, int $col, string $player): void
    {
        if (!$this->isValidMove($row, $col)) {
            throw new InvalidArgumentException("Invalid move at [$row, $col]");
        }

        $this->board[$row][$col] = $player;
    }
}
