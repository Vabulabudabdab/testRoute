<?php
if( !session_id() ) {session_start();}

require '../vendor/autoload.php';

use App\Exception\NotEnoughMoneyException;

function test($amount = 1) {

    $total = 10;

    if($amount > $total) {
        throw new NotEnoughMoneyException("Need more money");
        echo "123";
    }
}

try {
    test(25);
} catch (NotEnoughMoneyException $exception) {
    echo $exception->getMessage();
}


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', 'public', ['App/Controllers/HomeController', 'index']);
    $r->addRoute('GET', 'about', ['App/Controllers/HomeController', 'about']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "err";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $controller = new $handler[0];
        call_user_func([$vars]);
        // ... call $handler with $vars
        break;
}
