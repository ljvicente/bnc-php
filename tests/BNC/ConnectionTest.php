<?php

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

use ljvicente\BNC\Connection;

class ConnectionTest extends TestCase
{
    public function testCanBeInitialized()
    {
        $dotenv = new Dotenv(__DIR__.'/../..');
        $dotenv->load();

        $connection = new Connection(getenv('BNC_USER'), getenv('BNC_PASS'));
        $this->assertInstanceOf(Connection::class, $connection);
    }
}
