<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Minimax\MinimaxAssistant;
use App\Domain\Game\Board;

class MinimaxAssistantTest extends TestCase
{
    public function testAIPicksWinningMove()
    {
        $board = new Board();
        $board->setBoard([
            ['O','O',''],
            ['X','X',''],
            ['','','']
        ]);

        $ai = new MinimaxAssistant();
        $move = $ai->suggestMove($board);
        $this->assertEquals(['row' => 0, 'col' => 2], $move);
    }

    public function testAIPreventsLoss() {
        $board = new Board();
        $board->setBoard([
            ['X','X',''],
            ['O','',''],
            ['','','']
        ]);

        $ai = new MinimaxAssistant();
        $move = $ai->suggestMove($board);
        $this->assertEquals(['row' => 0, 'col' => 2], $move);
    }

    public function testAIPicksCenterIfAvailable()
    {
        $board = new Board();
        $board->setBoard([
            ['X','',''],
            ['','',''],
            ['','','O']
        ]);

        $ai = new MinimaxAssistant();
        $move = $ai->suggestMove($board);
        $this->assertContains($move, [
            ['row' => 1, 'col' => 1],
            ['row' => 0, 'col' => 2],
            ['row' => 0, 'col' => 0],
            ['row' => 2, 'col' => 0],
            ['row' => 2, 'col' => 2]
        ]);
    }


    public function testDrawScenaio()
    {
        $board = new Board();
        $board->setBoard([
            ['O','X','O'],
            ['O','X','X'],
            ['X','O','']
        ]);

                $ai = new MinimaxAssistant();
        $move = $ai->suggestMove($board);
        $this->assertEquals(['row' => 2, 'col' => 2], $move);
    }
}
