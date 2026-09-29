<?php

namespace App\BackEnd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;
use PDOException;

class CreateDatabaseCommand extends Command
{
    protected static $defaultName = 'app:create-database';

    protected function configure()
    {
        $this->setDescription('Creates the database defined in .env if it does not exist.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Database Creator',
            '================',
            '',
        ]);

        $host = getenv('DB_HOST');
        $name = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        $port = getenv('DB_PORT') ?: 3306;

        if (!$host || !$name || !$user) {
            $output->writeln('<error>Missing database configuration in .env file.</error>');
            return Command::FAILURE;
        }

        try {
            $output->writeln("Connecting to MySQL server at $host:$port...");
            $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $output->writeln("Creating database '$name' if it doesn't exist...");
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8 COLLATE utf8_unicode_ci");

            $output->writeln('<info>Database created successfully (or already exists).</info>');
            return Command::SUCCESS;

        } catch (PDOException $e) {
            $output->writeln('<error>Database creation failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
