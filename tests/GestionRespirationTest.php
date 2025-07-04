<?
// GestionRespirationTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . './controleur/gestionRespiration.php';

class GestionRespirationTest extends TestCase
{
    public function testStartRespiration()
    {
        $result = startRespiration(5); // start 5 min session
        $this->assertTrue($result);
    }
}