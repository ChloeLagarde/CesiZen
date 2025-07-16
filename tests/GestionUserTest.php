<?php
// tests/GestionUserTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controleur/gestionUser.php';

class GestionUserTest extends TestCase
{
    public function testVerifierAdmin()
    {
        // Test basique pour vérifier que les fonctions existent
        $this->assertTrue(function_exists('verifierAdmin'));
        $this->assertTrue(function_exists('isLastActiveAdmin'));
    }

    public function testFunctionsExist()
    {
        // Vérifier que les fonctions principales existent
        $this->assertTrue(function_exists('ajouterUtilisateur'));
        $this->assertTrue(function_exists('modifierUtilisateur'));
        $this->assertTrue(function_exists('supprimerUtilisateur'));
        $this->assertTrue(function_exists('desactiverUtilisateur'));
        $this->assertTrue(function_exists('activerUtilisateur'));
    }
}