<?php
require __DIR__.'/../vendor/autoload.php';

use App\KanbanBoard\Application;
use App\KanbanBoard\Authentication;
use App\KanbanBoard\GithubService;
use App\Support\Env;
use Dotenv\Dotenv;

// Loading .env file
Dotenv::create(Env::getRepository(), __DIR__.'/../', ['.env'])->load();

$authentication = new Authentication();
$token = $authentication->login();

$board = new Application(new GithubService($token));
$data = $board->board();

//var_dump($data);

$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../src/views'),
));
echo $m->render('index', array('milestones' => $data));
