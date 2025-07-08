<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Contracts\AIAssistant;
use App\Domain\Contracts\Storage;
use App\Domain\Game\Board;
use App\Shared\Http\JsonResponseFactory;
use App\Shared\Exception\InvalidMoveException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * BoardController Class
 *
 * @package App\Http\Controllers
 * @author  Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @link    https://www.en.dobrenteiistvan.hu
 */
class BoardController
{
    private const PLAYER_MARK = 'X';
    private const AI_MARK = 'O';

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Board $board
     * @param Storage $storage
     */
    public function __construct(
        private LoggerInterface $logger,
        private Board $board,
        private Storage $storage
    ) {
    }

    /**
     * __invoke
     * Initialize the Game table or reloading the page and create an existing table again
     *
     * @param ServerRequestInterface $request
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $tableId = isset($request->getQueryParams()['table']) ? $request->getQueryParams()['table'] : 'default';
        $inputBoardSize = isset($request->getQueryParams()['size']) ? (int)$request->getQueryParams()['size'] : 3;
        if ($this->storage->exists($tableId)) {
            $boardData = $this->storage->load($tableId);
            $this->board->setBoard($boardData);
            if (!empty($this->board->getWinner()) || $this->board->isFull()) {
                $this->board->createEmptyBoard($inputBoardSize);
                $boardData = $this->board->getBoard();
                $this->storage->save($tableId, $boardData);
            }
        } else {
            $this->board->createEmptyBoard($inputBoardSize);
            $boardData = $this->board->getBoard();
            $this->storage->save($tableId, $boardData);
        }

        return JsonResponseFactory::create($boardData, 200);
    }

    /**
     * Mark
     * Validate and store the player sign
     * Asking an AI assistant to make a sign on the board
     * return a json response
     *
     * example:
     * {
     *  "success": true,
     *  "result": {"col":1, "row":1},
     *  "gameover": false
     *  "winner": ""
     * }
     * @param ServerRequestInterface $request
     * @param AIAssistant $aiAssistant
     */
    public function mark(ServerRequestInterface $request, AIAssistant $aiAssistant): ResponseInterface
    {
        //get parameters and validate it
        $inputCol = isset($request->getParsedBody()['colIndex']) ? (int)$request->getParsedBody()['colIndex'] : '';
        $inputRow = isset($request->getParsedBody()['rowIndex']) ? (int)$request->getParsedBody()['rowIndex'] : '';
        $tableId = isset($request->getQueryParams()['table']) ? $request->getQueryParams()['table'] : 'default';

        $boardData = $this->storage->load($tableId);
        $this->board->setBoard($boardData);

        try {
            $this->board->applyMove($inputRow, $inputCol, self::PLAYER_MARK);
            $this->storage->save($tableId, $this->board->getBoard());
        } catch (InvalidArgumentException $e) {
            $result = BoardController::getResultValue(false, $e->getMessage());
            $this->logger->error('Board::applyMove params:', ['inputRow' => $inputRow, 'inputCol' => $inputCol]);
            $this->logger->error('BoardController::mark:', $result);
            return JsonResponseFactory::create($result, 200);
        }

        //Ask AI assistant to make a sign
        if (empty($this->board->getWinner()) && !$this->board->isFull()) {
            $modelName = $_ENV['OPENAI_MODEL_NAME'] ?? "";
            try {
                $move = $aiAssistant->suggestMove($this->board, $modelName);
                $col = $move['col'] ?? '';
                $row = $move['row'] ?? '';
                if ($_ENV['LOG_LEVEL'] == 'debug') {
                    $this->logger->debug('AIAssistant suggested move:', $move);
                }
                $this->board->applyMove($row, $col, self::AI_MARK);
                $this->storage->save($tableId, $this->board->getBoard());
                $result = BoardController::getResultValue(true, ['row' => $row, 'col' => $col]);
            } catch (InvalidMoveException | InvalidArgumentException $e) {
                $result = BoardController::getResultValue(false, $e->getMessage());
            }
        } else {
            $result = BoardController::getResultValue(true, []);
        }

        return JsonResponseFactory::create($result, 200);
    }

    /**
     * getResultValue
     *
     * @param boolean $success
     * @param mixed $data
     * @return array
     */
    private function getResultValue(bool $success, mixed $data): array
    {
        $winner = $this->board->getWinner();
        return [
            "success" => $success,
            "result" => $data,
            "gameover" => !empty($winner) || $this->board->isFull(),
            "winner" => $winner,
            "winner_cells" => "" //@todo implement returning the winner's cells
        ];
    }
}
