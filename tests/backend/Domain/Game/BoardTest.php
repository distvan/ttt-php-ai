<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Domain\Game\Board;

class BoardTest extends TestCase
{
    public function testXWinsByRow()
    {
        $data = [
            ['X', 'X', 'X'],
            ['', 'O', ''],
            ['O', '', 'O']
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->assertEquals('X', $board->getWinner());
    }

    public function testOWinsByColumn()
    {
        $data = [
            ['O', 'X', 'X'],
            ['O', '', 'X'],
            ['O', '', '']
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->assertEquals('O', $board->getWinner());
    }

    public function testXWinsByMainDiagonal()
    {
        $data = [
            ['X', 'O', ''],
            ['O', 'X', 'O'],
            ['O', '', 'X']
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->assertEquals('X', $board->getWinner());
    }

    public function testOWinsByAntiDiagonal()
    {
        $data = [
            ['X', 'O', 'O'],
            ['X', 'O', ''],
            ['O', 'O', '']
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->assertEquals('O', $board->getWinner());
    }

    public function testHasNoWinner()
    {
        $data = [
            ['X', 'O', 'X'],
            ['O', 'X', 'O'],
            ['O', 'X', 'O']
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->assertEmpty($board->getWinner());
        $this->assertTrue($board->hasNoWinnerButBoardIsFull());
    }

    public function testGameStillInProgress()
    {
        $data = [
            ['X', 'O', ''],
            ['O', 'X', ''],
            ['', '', '']
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->assertEmpty($board->getWinner());
        $this->assertFalse($board->hasNoWinnerButBoardIsFull());
    }

    public function testNonSquareBoard()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Board must be square");

        $data = [
            ['X', 'O', ''],
            ['O', 'X'],
            ['', '', '']
        ];

        $board = new Board();
        $board->setBoard($data);
    }

    public function testTooSmallBoard()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Board must be at least 3x3.");

        $data = [
            ['X', 'O'],
            ['O', 'X']
        ];

        $board = new Board();
        $board->setBoard($data);
    }

    public function testInvalidMoveThrowsException()
    {
        $data = [
            ['X', '',  'O'],
            ['',  '',  ''],
            ['',  '',  ''],
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid move at [0, 0]");

        $board->applyMove(0, 0, 'X');
    }

    public function testOutOfBoundsMoveThrowsException()
    {
        $data = [
            ['X', '',  'O'],
            ['',  '',  ''],
            ['',  '',  ''],
        ];

        $board = new Board();
        $board->setBoard($data);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid move at [3, 3]");

        $board->applyMove(3, 3, 'O');
    }
}
