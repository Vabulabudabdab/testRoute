<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<?php
if( !session_id() ) {session_start();}

require '../vendor/autoload.php';
use Aura\SqlQuery\QueryFactory;
use JasonGrimes\Paginator;
use DI\ContainerBuilder;
use Delight\Auth\Auth;
use League\Plates\Engine;
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Engine::class => function () {
        return new Engine('../app/Views');
    },
    PDO::class => function () {
        $driver = "mysql";
        $host = "localhost";
        $database_name = "SecondProject";
        $username = "root";
        $password = "";

        return new PDO("$driver:host=$host; dbname=$database_name", $username, $password);

    },

    Auth::class => function ($container) {
        return new Auth($container->get('PDO'));
    }

]);
$container = $containerBuilder->build();


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
        $r->addRoute('GET', '/', ['App\Controllers\HomeController', 'index']);
        $r->addRoute('GET', '/about', ['App\Controllers\HomeController', 'about']);
        $r->addRoute('GET', '/verification', ['App\Controllers\HomeController', 'email_verification']);
        $r->addRoute('GET', '/login', ['App\Controllers\HomeController', 'login']);
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
        echo "error 404?..";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed\
        echo "Ошибка 405";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($routeInfo[1], $routeInfo[2]);
        break;
}


//d(SimpleMail::make()
//    ->setTo('test@gmail.com', 'Test')
//    ->setFrom('admin@example.com', 'Admin')
//    ->setSubject('Test')
//    ->setMessage('Hello')
//    ->send());


$pdo = new PDO('mysql:host=localhost;dbname=SecondProject', 'root','');
$queryFactory = new QueryFactory('mysql');

$select = $queryFactory->newSelect();
$select
    ->cols(['*'])
    ->from('posts');

$sth = $pdo->prepare($select->getStatement());

$sth->execute($select->getBindValues());

$totalItems = $sth->fetchAll(PDO::FETCH_ASSOC);

$select = $queryFactory->newSelect();

$select
    ->cols(['*'])
    ->from('posts')
    ->setPaging(3)
    ->page($_GET['page'] ?? 1);

$sth = $pdo->prepare($select->getStatement());

$sth->execute($select->getBindValues());

$items = $sth->fetchAll(PDO::FETCH_ASSOC);

$itemsPerPage = 3;
$currentPage = $_GET['page'] ?? 1;
$urlPattern = '?page=(:num)';

$paginator = new Paginator(count($totalItems), $itemsPerPage, $currentPage, $urlPattern);
foreach ($items as $item) {
    echo $item['id'] . PHP_EOL . $item['title'] . '<br>';
}
?>

<ul class="pagination">
    <?php if ($paginator->getPrevUrl()): ?>
        <li><a href="<?php echo $paginator->getPrevUrl(); ?>">&laquo; Previous</a></li>
    <?php endif; ?>

    <?php foreach ($paginator->getPages() as $page): ?>
        <?php if ($page['url']): ?>
            <li <?php echo $page['isCurrent'] ? 'class="active"' : ''; ?>>
                <a href="<?php echo $page['url']; ?>"><?php echo $page['num']; ?></a>
            </li>
        <?php else: ?>
            <li class="disabled"><span><?php echo $page['num']; ?></span></li>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($paginator->getNextUrl()): ?>
        <li><a href="<?php echo $paginator->getNextUrl(); ?>">Next &raquo;</a></li>
    <?php endif; ?>
</ul>

<p>
    <?php echo $paginator->getTotalItems(); ?> found.

    Showing
    <?php echo $paginator->getCurrentPageFirstItem(); ?>
    -
    <?php echo $paginator->getCurrentPageLastItem(); ?>.
</p>
