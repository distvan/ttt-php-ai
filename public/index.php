<?php

/**
 * TicTacToe with AI
 * php version 8.1
 *
 * @category PHP_with_AI
 * @package  PHP_AI
 * @author   Istvan Dobrentei <info@dobrenteiistvan.hu>
 * @license  https://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     https://www.en.dobrenteiistvan.hu
 */

declare(strict_types=1);

use App\Application\Application;
use App\Application\Contracts\AIAssistant as AIAssistantInterface;
use App\Domain\Contracts\Storage;
use App\Domain\Game\Board;
use App\Infrastructure\Container\Container;
use App\Infrastructure\Http\Dispatcher;
use App\Infrastructure\Http\Routing\Router;
use App\Shared\Logging\LoggerFactory;
use Psr\Http\Message\ServerRequestInterface;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\BoardController;
use App\Infrastructure\Kernel\Kernel;
use App\Shared\Config\Config;
use App\Shared\View;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
$config = new Config(__DIR__ . '/../config');

//load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

//init kernel, container
$container = new Container();
$kernel = new Kernel($container);

//register providers
$kernel->registerProviders($config->get('providers'));

//instantiate components
$router = new Router();
$router->add('GET', '/', function(ServerRequestInterface $request) use ($container) {
    $controller = new IndexController(
        new View(__DIR__ . '/../src/Http/Views', __DIR__ . '/../src/Http/Views/layouts'),
        $container->get(Storage::class)
    );
    return $controller($request);
});

$router->add('GET', '/init-board', function(ServerRequestInterface $request) use ($container) {
    $controller = new BoardController(
        LoggerFactory::create(),
        new Board(),
        $container->get(Storage::class)
    );
    return $controller($request);
});

$router->add('POST', '/mark', function(ServerRequestInterface $request) use ($container) {
    $controller = new BoardController(
        LoggerFactory::create(),
        new Board(),
        $container->get(Storage::class)
    );
    return $controller->mark($request, $container->get(AIAssistantInterface::class));
});

//create and run app
$application = new Application(new Dispatcher($router));
$psr17 = new Psr17Factory();
$application->run((new ServerRequestCreator($psr17, $psr17, $psr17, $psr17))->fromGlobals());
