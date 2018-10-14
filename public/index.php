<?php


ini_set('display_errors',1);
ini_set('display_starup_error',1);
error_reporting(E_ALL);
require '../vendor/autoload.php';
session_start();

$dotenv = new Dotenv\Dotenv(__DIR__.'/..');
$dotenv->load();

use Aura\Router\RouterContainer;
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();
$map = $routerContainer->getMap();
$map->get('index', '/platzi/intro/', [
    'controller' => 'App\Controllers\IndexController',
    'action' => 'indexAction',
    'auth' => true
]);
$map->get('addJobs', '/platzi/intro/jobs/add',  [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction',
    'auth' => true
]);
$map->post('saveJobs', '/platzi/intro/jobs/add',  [
    'controller' => 'App\Controllers\JobsController',
    'action' => 'getAddJobAction',
    'auth' => true
]);
$map->get('signinG', '/platzi/intro/signin',  [
    'controller' => 'App\Controllers\SignInController',
    'action' => 'getSignInAction'
]);
$map->get('logout', '/platzi/intro/logout',  [
    'controller' => 'App\Controllers\SignInController',
    'action' => 'getLogout',
    'auth' => true
]);
$map->post('signinP', '/platzi/intro/signin',  [
    'controller' => 'App\Controllers\SignInController',
    'action' => 'getSignInAction'
]);
$map->get('signupG', '/platzi/intro/signup',  [
    'controller' => 'App\Controllers\SignUpController',
    'action' => 'getSignUpAction'
]);
$map->post('signupP', '/platzi/intro/signup',  [
    'controller' => 'App\Controllers\SignUpController',
    'action' => 'getSignUpAction'
]);
$map->get('admin', '/platzi/intro/admin', [
    'controller' => 'App\Controllers\AdminController',
    'action' => 'getIndex',
    'auth' => true
]);
$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);

if (!$route){
    echo 'No route<br>';
}else{
    $handler = $route->handler;
    $actionName = $handler['action'];
    $controllerName = $handler['controller'];
    $needsAuth = $handler['auth'] ?? false;
    if ($needsAuth && !isset($_SESSION['userID'])){
        echo 'redirect';
        header('Location: /platzi/intro/signin',false);
    }else{
        $controller = new $controllerName;
        $response = $controller->$actionName($request);
        foreach ($response->getHeaders() as $name => $values) {
            foreach($values as $value){
                header(sprintf('%s: %s',$name, $value),false);
            }
        }
        http_response_code($response->getStatusCode());
        echo $response->getBody();
    }
    
}

