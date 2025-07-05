<?php

declare(strict_types=1);

namespace App\Domain\Game;

use InvalidArgumentException;

/**
 * Board Class
 *
 * @package App\Domain\Game
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class Board
{
    private array $board = [];
    private int $size = 3;

    /**
     * Create an empty board
     *
     * @param integer $size
     * @return void
     */
    public function createEmptyBoard(int $size = 3): void
    {
        for ($i = 0; $i < $size; $i++) {
            $this->board[$i] = array_fill(0, $size, '');
        }
        $this->size = $size;
    }

    /**
     * Get board
     *
     * @return array
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
    public function setBoard(array $board): void
    {
        Board::validateBoard($board);
        $this->board = $board;
        $this->size = count($board);
    }

    /**
     * ValidateBoard
     *
     * @param $board
     * @throws InvalidArgumentException
     */
    public static function validateBoard(array $board): void
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
     * getWinner
     *
     * @return string the winner's player sign (X,O or empty)
     */
    public function getWinner(): string
    {
        $lines = array_merge(
            $this->board,
            $this->getColumns(),
            $this->getDiagonals()
        );

        foreach ($lines as $line) {
            if (!empty($line[0]) && Board::allEqual($line)) {
                return $line[0];
            }
        }

        return "";
    }

    /**
     * getColumns
     *
     * @return array
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
     * getDiagonals
     *
     * @return array
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
    private static function allEqual(array $cells): bool
    {
        return count(array_unique($cells)) === 1;
    }

    /**
     * hasNoWinnerButBoardIsFull
     *
     * @return boolean
     */
    public function hasNoWinnerButBoardIsFull(): bool
    {
        return empty($this->getWinner()) && $this->isFull();
    }

    /**
     * isFull
     *
     * @return boolean
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
    public function isValidMove(int $row, int $col): bool
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
