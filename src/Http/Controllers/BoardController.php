<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Contracts\AIAssistant;
use App\Domain\Contracts\Storage;
use App\Domain\Game\Board;
use App\Shared\Http\JsonResponseFactory;
use App\Shared\InvalidMoveException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * BoardController
 *
 * @package App\Http\Controllers
 */
class BoardController
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param AIService $aiService
     */
    public function __construct(
        private LoggerInterface $logger,
        private Board $board,
        private Storage $storage
    ) {
    }

    /**
     * __invoke
     *
     * @param ServerRequestInterface $request
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $inputBoardSize = isset($request->getQueryParams()['size']) ? (int)$request->getQueryParams()['size'] : 3;
        if ($this->storage->exists('board-data')) {
            $boardData = $this->storage->load('board-data');
            $this->board->setBoard($boardData);
            if (!empty($this->board->getWinner()) || $this->board->isFull()) {
                $this->board->createEmptyBoard($inputBoardSize);
                $boardData = $this->board->getBoard();
                $this->storage->save('board-data', $boardData);
            }
        } else {
            $this->board->createEmptyBoard($inputBoardSize);
            $boardData = $this->board->getBoard();
            $this->storage->save('board-data', $boardData);
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
        $result['winner'] = "";
        $result['gameover'] = false;

        //get parameters and validate it
        $inputCol = isset($request->getParsedBody()['colIndex']) ? (int)$request->getParsedBody()['colIndex'] : '';
        $inputRow = isset($request->getParsedBody()['rowIndex']) ? (int)$request->getParsedBody()['rowIndex'] : '';

        $boardData = $this->storage->load('board-data');
        $this->board->setBoard($boardData);

        try {
            $this->board->applyMove($inputRow, $inputCol, 'X');
            $this->storage->save('board-data', $this->board->getBoard());
            $result['success'] = true;
            $result['result'] = $this->board->getBoard();
        } catch (InvalidArgumentException $e) {
            $result['success'] = false;
            $result['result'] = $e->getMessage();
            $this->logger->error('Board::applyMove params:', ['inputRow' => $inputRow, 'inputCol' => $inputCol]);
            $this->logger->error('BoardController::mark:', $result);
            return JsonResponseFactory::create($result, 200);
        }

        //check winner
        $result['winner'] = $this->board->getWinner();
        if (empty($result['winner'])) {
            $result['gameover'] = $this->board->isFull();
        } else {
            $result['gameover'] = true;
        }

        //Ask AI assistant to make a sign
        if (empty($result['winner']) && !$result['gameover']) {
            $modelName = $_ENV['OPENAI_MODEL_NAME'] ?? "";
            try {
                $move = $aiAssistant->suggestMove($this->board->getBoard(), $modelName);
                $col = $move['col'] ?? '';
                $row = $move['row'] ?? '';
                if ($_ENV['LOG_LEVEL'] == 'debug') {
                    $this->logger->debug('AIAssistant suggested move:', $move);
                }
                $this->board->applyMove($row, $col, 'O');
                $this->storage->save('board-data', $this->board->getBoard());
                $result['success'] = true;
                $result['result']['col'] = $col;
                $result['result']['row'] = $row;

                //check winner
                $result['winner'] = $this->board->getWinner();
                if (empty($result['winner'])) {
                    $result['gameover'] = $this->board->isFull();
                } else {
                    $result['gameover'] = true;
                    //@Todo: implementing winner cells
                    $result['winner_cells'] = "";
                }
            } catch (InvalidMoveException | InvalidArgumentException $e) {
                $result['success'] = false;
                $result['result'] = $e->getMessage();
            }
        }

        return JsonResponseFactory::create($result, 200);
    }
}
