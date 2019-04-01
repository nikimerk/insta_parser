<?php
define('ROOT', dirname(__FILE__));
$url_arr = explode('/', $_SERVER['REQUEST_URI']);
require_once (ROOT.'/vendor/autoload.php');

//if($url_arr[1] != 'admin') {
//    require_once(ROOT.'/eng-jobs/index.html');
//    exit;
//}
// FRONT CONTROLLER

// Общие настройки
ini_set('display_errors',1);
error_reporting(E_ALL);

session_start();

// Подключение файлов системы

require_once(ROOT.'/components/Autoload.php');


// Вызов Router
$router = new Router();
$router->run();