<?
// GestionRessourcesTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . './controleur/gestionRessources.php';

class GestionRessourcesTest extends TestCase
{
    public function testAddRessource()
    {
        $data = ['title' => 'testRessource', 'content' => 'Some content'];
        $result = addRessource($data);
        $this->assertTrue($result);
    }

    public function testModifyRessource()
    {
        $data = ['id' => 1, 'title' => 'updatedTitle'];
        $result = modifyRessource($data);
        $this->assertTrue($result);
    }

    public function testDeleteRessource()
    {
        $result = deleteRessource(1);
        $this->assertTrue($result);
    }

    public function testListRessources()
    {
        $result = listRessources();
        $this->assertIsArray($result);
    }
}