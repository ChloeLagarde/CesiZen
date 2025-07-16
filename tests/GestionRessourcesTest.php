<?php
// tests/GestionRessourcesTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controleur/gestionRessources.php';

class GestionRessourcesTest extends TestCase
{
    private $gestionRessources;
    
    protected function setUp(): void
    {
        // Mock la classe pour éviter les problèmes de base de données
        $this->gestionRessources = $this->getMockBuilder(GestionRessources::class)
                                        ->disableOriginalConstructor()
                                        ->getMock();
    }

    public function testRecuperationRessources()
    {
        $mockData = [
            ['id' => 1, 'titre' => 'Test Resource', 'description' => 'Test Description']
        ];
        
        $this->gestionRessources->method('recupererRessources')
                               ->willReturn($mockData);
        
        $result = $this->gestionRessources->recupererRessources(1, 'admin');
        $this->assertIsArray($result);
    }
}