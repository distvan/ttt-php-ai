<?php

namespace App\Application\Services;

use App\Application\Contracts\AIAssistant as AIAssistantInterface;
use App\Infrastructure\OpenAI\OpenAIClient;
use App\Shared\Exception\InvalidMoveException;

/**
 * AIAssistant Service
 *
 * @package App\Application\Services
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class AIAssistant implements AIAssistantInterface
{
    /**
     * Constructor
     *
     * @param OpenAIClient $openAIClient
     */
    public function __construct(
        private OpenAIClient $openAIClient
    ) {
    }

    /**
     * SuggestMove
     *
     * @param array $board
     * @param string $model name of the applied AI model
     * @return array
     * @throws InvalidMoveException
     */
    public function suggestMove(array $board, $model): array
    {
        $prompt = AIAssistant::generateTicTacToePrompt($board);

        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful AI assistant.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $response = $this->openAIClient->chat($messages, $model);

        $move = AIAssistant::cleanJsonBlock($response);
        if (!isset($move['row'], $move['col'])) {
            throw new InvalidMoveException('Invalid move response from AI');
        }

        return $move;
    }

    /**
     * cleanJsonBlock
     * clean the AI response and decode it from json
     *
     * @param string $input
     * @return array
     */
    private static function cleanJsonBlock(string $input): array
    {
        $input = trim($input);

        // Remove starting ```json or ```
        if (str_starts_with($input, '```json')) {
            $input = substr($input, 7); // length of '```json'
        } elseif (str_starts_with($input, '```')) {
            $input = substr($input, 3);
        }

        // Remove ending ```
        if (str_ends_with($input, '```')) {
            $input = substr($input, 0, -3);
        }
        $input = trim($input);
        $data = json_decode($input, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        return [];
    }

    /**
     * GenerateTicTacToePrompt
     * Provide a prompt for the AI assistant
     *
     * @param array $board
     * @return string
     */
    private static function generateTicTacToePrompt(array $board): string
    {
        $boardJson = json_encode($board, JSON_PRETTY_PRINT);

        return <<<EOT
                You are a Tic Tac Toe master. Given the current board state, return the best move in JSON format.
                Use the following board format:

                {$boardJson}

                Where:
                    - "X" is the player's move
                    - "O" is the opponent's move
                    - "" is an empty cell

                Return a single move in this JSON format:

                {
                    "row": <row_index>,
                    "col": <col_index>
                }

                Only respond with valid JSON and nothing else.
            EOT;
    }
}
