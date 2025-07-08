<?php
// tests/DatabaseTest.php
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/config/database.php';

class DatabaseTest extends TestCase
{
    private $database;
    
    protected function setUp(): void
    {
        $this->database = new Database();
    }
    
    public function testDatabaseConnection()
    {
        $connection = $this->database->getConnection();
        $this->assertNotNull($connection);
        $this->assertInstanceOf(PDO::class, $connection);
    }
    
    public function testDatabaseStructure()
    {
        $connection = $this->database->getConnection();
        
        // Test que la table users existe
        $stmt = $connection->query("SHOW TABLES LIKE 'users'");
        $this->assertEquals(1, $stmt->rowCount(), "La table 'users' doit exister");
        
        // Test que la table ressources existe
        $stmt = $connection->query("SHOW TABLES LIKE 'ressources'");
        $this->assertEquals(1, $stmt->rowCount(), "La table 'ressources' doit exister");
    }
    
    public function testUserTableStructure()
    {
        $connection = $this->database->getConnection();
        
        // Vérifier la structure de la table users
        $stmt = $connection->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $expectedColumns = ['id', 'username', 'email', 'password', 'role'];
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $columns, "La colonne '$column' doit exister dans la table users");
        }
    }
    
    protected function tearDown(): void
    {
        $this->database = null;
    }
}