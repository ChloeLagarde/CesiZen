<?php
// tests/DatabaseTest.php
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testDatabaseClassExists()
    {
        // Inclure le fichier database uniquement si la classe n'existe pas
        if (!class_exists('Database')) {
            require_once dirname(__DIR__) . '/config/database.php';
        }
        
        $this->assertTrue(class_exists('Database'));
    }
    
    public function testDatabaseCanBeInstantiated()
    {
        // Inclure le fichier database uniquement si la classe n'existe pas
        if (!class_exists('Database')) {
            require_once dirname(__DIR__) . '/config/database.php';
        }
        
        $database = new Database();
        $this->assertInstanceOf(Database::class, $database);
    }
    
    public function testDatabaseConnectionMethodExists()
    {
        // Inclure le fichier database uniquement si la classe n'existe pas
        if (!class_exists('Database')) {
            require_once dirname(__DIR__) . '/config/database.php';
        }
        
        $database = new Database();
        $this->assertTrue(method_exists($database, 'getConnection'));
    }
    
    public function testPDOClassExists()
    {
        $this->assertTrue(class_exists('PDO'));
    }
}