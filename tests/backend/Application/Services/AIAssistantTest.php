<?php

use PHPUnit\Framework\TestCase;
use App\Application\Services\AIAssistant;
use App\Infrastructure\OpenAI\OpenAIClient;
use App\Shared\Exception\InvalidMoveException;
use App\Domain\Game\Board;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 4));
$dotenv->load();
class AIAssistantTest extends TestCase
{
    public function testSuggestMoveReturnsValidMove()
    {
        $board = new Board();
        $board->setBoard([
            ['X', 'O', 'X'],
            ['',  'O', ''],
            ['',  '',  ''],
        ]);

        $expectedJson = json_encode(['row' => 2, 'col' => 0]);

        /** @var OpenAiClient | \PHPUnit\Framework\MockObject\MockObject $mockClient */
        $mockClient = $this->getMockBuilder(OpenAIClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['chat'])
            ->getMock();

        $mockClient->expects($this->once())
            ->method('chat')
            ->with($this->callback(function ($messages) {
                return is_array($messages) && count($messages) === 2;
            }))
            ->willReturn($expectedJson);

        $ai = new AIAssistant($mockClient);

        $result = $ai->suggestMove($board, $_ENV['OPENAI_MODEL_NAME']);

        $this->assertIsArray($result);
        $this->assertSame(['row' => 2, 'col' => 0], $result);
    }

    public function testSuggestMoveThrowsOnInvalidResponse()
    {
        $board = new Board();
        $board->setBoard([
            ['X', 'O', ''],
            ['',  '',  ''],
            ['',  '',  ''],
        ]);

        $invalidResponse = '{"invalid": "data"}';

        /** @var OpenAiClient | \PHPUnit\Framework\MockObject\MockObject $mockClient */
        $mockClient = $this->getMockBuilder(OpenAIClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['chat'])
            ->getMock();

        $mockClient->expects($this->once())
            ->method('chat')
            ->willReturn($invalidResponse);

        $ai = new AIAssistant($mockClient);

        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage('Invalid move response from AI');

        $ai->suggestMove($board, $_ENV['OPENAI_MODEL_NAME']);
    }
}
