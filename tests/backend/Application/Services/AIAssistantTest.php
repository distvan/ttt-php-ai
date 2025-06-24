<?php

use PHPUnit\Framework\TestCase;
use App\Application\Services\AIAssistant;
use App\Infrastructure\OpenAI\OpenAIClient;
use App\Shared\InvalidMoveException;

class AIAssistantTest extends TestCase
{
    public function testSuggestMoveReturnsValidMove()
    {
        $board = [
            ['X', 'O', 'X'],
            ['',  'O', ''],
            ['',  '',  ''],
        ];

        $expectedJson = json_encode(['row' => 2, 'col' => 0]);

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

        $result = $ai->suggestMove($board);

        $this->assertIsArray($result);
        $this->assertSame(['row' => 2, 'col' => 0], $result);
    }

    public function testSuggestMoveThrowsOnInvalidResponse()
    {
        $board = [
            ['X', 'O', ''],
            ['',  '',  ''],
            ['',  '',  ''],
        ];

        $invalidResponse = '{"invalid": "data"}';

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

        $ai->suggestMove($board);
    }
}
