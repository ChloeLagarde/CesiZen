<?php
// tests/GestionRespirationTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controleur/gestionRespiration.php';

class GestionRespirationTest extends TestCase
{
    public function testGetExercices()
    {
        $gestionRespiration = new GestionRespiration();
        $result = $gestionRespiration->getExercices();
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }
}