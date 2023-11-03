<?php
require_once __DIR__ . "/vendor/autoload.php";
use Schorsch3000\RusticRouter\Router;

$router = new Router();

$router->addRoute("GET", "/hello", function ($data, $num) {
    echo "Hello World!";
});




$router->addRoute("GET", "~/\\/hello\/(?<name>[A-Z][a-z]+)/", function ($data, $num) {
    echo "~Hello ".$data['name']."!";

});

$router->addRoute("GET", ":/hello/:name:", function ($data, $num) {
    echo ":Hello ".$data['name']."!";

});

$router->run();
