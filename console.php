#!/usr/bin/env php
<?php
// application.php



require __DIR__.'/vendor/autoload.php';

use App\Commands\CreateUserCommand;
use App\Commands\SendMailCommand;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Application;

$dotenv = Dotenv::createImmutable(__DIR__ );
$dotenv->load();

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
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

$application = new Application();
$application->add(new SendMailCommand());
$application->add(new CreateUserCommand());
$application->run();
