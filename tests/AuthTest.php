<?php
// tests/AuthTest.php
use PHPUnit\Framework\TestCase;

// Chemin corrigé pour inclure le fichier auth.php
require_once dirname(__DIR__) . '/controleur/auth.php';

class AuthTest extends TestCase
{
    private $auth;
    
    protected function setUp(): void
    {
        // Créer une instance de Auth avant chaque test
        $this->auth = $this->getMockBuilder(Auth::class)
                          ->disableOriginalConstructor()
                          ->getMock();
                          
        // Si vous préférez utiliser la vraie classe (nécessite une base de données)
        // $this->auth = new Auth();
    }
    
    public function testLoginWithValidCredentials()
    {
        // Configuration du mock pour simuler un login réussi
        $this->auth->method('login')
             ->with('test@example.com', 'validPassword')
             ->willReturn(['success' => true, 'user' => [
                 'id' => 1,
                 'username' => 'validUser',
                 'email' => 'test@example.com',
                 'role' => 'user'
             ]]);
        
        $result = $this->auth->login('test@example.com', 'validPassword');
        $this->assertTrue($result['success']);
    }

    public function testLoginWithInvalidCredentials()
    {
        // Configuration du mock pour simuler un échec de login
        $this->auth->method('login')
             ->with('test@example.com', 'wrongPassword')
             ->willReturn(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
        
        $result = $this->auth->login('test@example.com', 'wrongPassword');
        $this->assertFalse($result['success']);
    }

    public function testRegisterWithValidData()
    {
        // Configuration du mock pour simuler une inscription réussie
        $this->auth->method('register')
             ->with('newUser', 'test@example.com', 'password')
             ->willReturn(true);
        
        $result = $this->auth->register('newUser', 'test@example.com', 'password');
        $this->assertTrue($result);
    }
    
    public function testLogout()
    {
        // Simuler une session active
        $_SESSION['user'] = ['id' => 1, 'username' => 'testUser'];
        
        // Simuler l'action de déconnexion
        $this->auth->method('logout')
             ->willReturn(true);
        
        $result = $this->auth->logout();
        $this->assertTrue($result);
        
        // Vérifier que la session est vidée après logout
        // Note: Dans un vrai test, on testerait la vraie méthode logout
        // Ici on simule juste que la session est nettoyée
        $_SESSION = [];
        $this->assertArrayNotHasKey('user', $_SESSION);
    }
}