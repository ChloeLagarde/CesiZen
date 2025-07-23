<?php
// tests/IntegrationTest.php
use PHPUnit\Framework\TestCase;

class IntegrationTest extends TestCase
{
    private $db;
    private $auth;
    private $gestionRessources;
    private static $testUserId;
    private static $testRessourceId;

    protected function setUp(): void
    {
        // Configuration pour les tests d'intégration
        $_ENV['DB_HOST'] = '127.0.0.1';
        $_ENV['DB_NAME'] = 'cesizentest';
        $_ENV['DB_USER'] = 'cesizen';
        $_ENV['DB_PASS'] = 'cesizen123';
        $_ENV['DB_PORT'] = '3306';

        // Inclure les classes nécessaires
        require_once dirname(__DIR__) . '/config/database.php';
        require_once dirname(__DIR__) . '/controleur/auth.php';
        require_once dirname(__DIR__) . '/controleur/gestionRessources.php';

        // Initialiser les objets
        $database = new Database();
        $this->db = $database->getConnection();
        $this->auth = new Auth();
        $this->gestionRessources = new GestionRessources();

        // Nettoyer les données de test précédentes
        $this->cleanupTestData();
    }

    protected function tearDown(): void
    {
        // Nettoyer après chaque test
        $this->cleanupTestData();
    }

    private function cleanupTestData()
    {
        // Supprimer les utilisateurs de test
        $stmt = $this->db->prepare("DELETE FROM users WHERE email LIKE 'test_%@integration.test'");
        $stmt->execute();

        // Supprimer les ressources de test
        $stmt = $this->db->prepare("DELETE FROM ressources WHERE titre LIKE 'Test Integration %'");
        $stmt->execute();
    }

    /**
     * Test d'intégration complet : Inscription -> Connexion -> Création ressource -> Consultation
     */
    public function testFluxCompletUtilisateur()
    {
        // === ÉTAPE 1 : INSCRIPTION ===
        $testEmail = 'test_user_' . time() . '@integration.test';
        $testUsername = 'testuser_' . time();
        $testPassword = 'TestPassword123!';

        $inscriptionResult = $this->auth->register($testUsername, $testEmail, $testPassword);
        $this->assertTrue($inscriptionResult, "L'inscription devrait réussir");

        // Vérifier que l'utilisateur existe en base
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$testEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertNotFalse($user, "L'utilisateur devrait exister en base");
        $this->assertEquals($testUsername, $user['username']);
        $this->assertEquals('utilisateur', $user['role']);
        self::$testUserId = $user['id'];

        // === ÉTAPE 2 : CONNEXION ===
        $connexionResult = $this->auth->login($testEmail, $testPassword);
        
        $this->assertIsArray($connexionResult, "Le résultat de connexion devrait être un tableau");
        $this->assertTrue($connexionResult['success'], "La connexion devrait réussir");
        $this->assertEquals($testEmail, $connexionResult['user']['email']);

        // Simuler la session pour les tests suivants
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user'] = $connexionResult['user'];

        // === ÉTAPE 3 : CRÉER UN ADMIN POUR POUVOIR GÉRER LES RESSOURCES ===
        $adminEmail = 'test_admin_' . time() . '@integration.test';
        $adminUsername = 'testadmin_' . time();
        
        // Créer admin directement en base (car register ne permet que des utilisateurs normaux)
        $hashedPassword = password_hash($testPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role, est_actif) VALUES (?, ?, ?, 'admin', 1)");
        $stmt->execute([$adminUsername, $adminEmail, $hashedPassword]);
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$adminEmail]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Se connecter en tant qu'admin
        $_SESSION['user'] = [
            'id' => $admin['id'],
            'username' => $admin['username'],
            'email' => $admin['email'],
            'role' => 'admin'
        ];

        // === ÉTAPE 4 : CRÉATION D'UNE RESSOURCE ===
        $ressourceTitre = 'Test Integration Ressource ' . time();
        $ressourceDescription = 'Description de test pour l\'intégration';
        $ressourceCategories = 'Test,Integration';

        // Insérer la ressource directement (simuler l'ajout par admin)
        $stmt = $this->db->prepare("INSERT INTO ressources (titre, description, categories, uploadé_par, est_visible) VALUES (?, ?, ?, ?, 1)");
        $result = $stmt->execute([$ressourceTitre, $ressourceDescription, $ressourceCategories, $admin['id']]);
        
        $this->assertTrue($result, "L'ajout de ressource devrait réussir");
        
        // Récupérer l'ID de la ressource créée
        self::$testRessourceId = $this->db->lastInsertId();

        // === ÉTAPE 5 : CONSULTATION DES RESSOURCES ===
        $ressources = $this->gestionRessources->recupererRessources($admin['id'], 'admin');
        
        $this->assertIsArray($ressources, "La récupération des ressources devrait retourner un tableau");
        $this->assertNotEmpty($ressources, "Il devrait y avoir au moins une ressource");

        // Vérifier que notre ressource de test est présente
        $ressourceTrouvee = false;
        foreach ($ressources as $ressource) {
            if ($ressource['titre'] === $ressourceTitre) {
                $ressourceTrouvee = true;
                $this->assertEquals($ressourceDescription, $ressource['description']);
                $this->assertEquals($ressourceCategories, $ressource['categories']);
                break;
            }
        }
        $this->assertTrue($ressourceTrouvee, "La ressource créée devrait être trouvée dans la liste");

        // === ÉTAPE 6 : TESTER EN TANT QU'UTILISATEUR NORMAL ===
        // Revenir en tant qu'utilisateur normal
        $_SESSION['user'] = $connexionResult['user'];
        
        $ressourcesUtilisateur = $this->gestionRessources->recupererRessources(self::$testUserId, 'utilisateur');
        $this->assertIsArray($ressourcesUtilisateur, "Un utilisateur normal devrait pouvoir voir les ressources");
        
        // Vérifier qu'il peut voir les ressources visibles
        $ressourceVisibleTrouvee = false;
        foreach ($ressourcesUtilisateur as $ressource) {
            if ($ressource['titre'] === $ressourceTitre) {
                $ressourceVisibleTrouvee = true;
                break;
            }
        }
        $this->assertTrue($ressourceVisibleTrouvee, "L'utilisateur normal devrait voir la ressource visible");
    }

    /**
     * Test des connexions incorrectes
     */
    public function testConnexionsIncorrectes()
    {
        // Test avec email inexistant
        $result = $this->auth->login('inexistant@test.com', 'motdepasse');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertContains('incorrect', strtolower($result['message']));

        // Test avec mot de passe incorrect
        $testEmail = 'test_wrong_pwd_' . time() . '@integration.test';
        $this->auth->register('testwrong', $testEmail, 'CorrectPassword');
        
        $result = $this->auth->login($testEmail, 'WrongPassword');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
    }

    /**
     * Test de la gestion des utilisateurs inactifs
     */
    public function testUtilisateurInactif()
    {
        // Créer un utilisateur
        $testEmail = 'test_inactive_' . time() . '@integration.test';
        $this->auth->register('testinactive', $testEmail, 'password');
        
        // Le désactiver en base
        $stmt = $this->db->prepare("UPDATE users SET est_actif = 0 WHERE email = ?");
        $stmt->execute([$testEmail]);
        
        // Tenter de se connecter
        $result = $this->auth->login($testEmail, 'password');
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertContains('désactivé', $result['message']);
    }

    /**
     * Test de la base de données
     */
    public function testConnexionBaseDeDonnees()
    {
        $this->assertNotNull($this->db, "La connexion à la base de données devrait être établie");
        
        // Test d'une requête simple
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('count', $result);
        $this->assertIsNumeric($result['count']);
    }

    /**
     * Test des exercices de respiration
     */
    public function testExercicesRespiration()
    {
        require_once dirname(__DIR__) . '/controleur/gestionRespiration.php';
        
        $gestionRespiration = new GestionRespiration();
        $exercices = $gestionRespiration->getExercices();
        
        $this->assertIsArray($exercices);
        $this->assertNotEmpty($exercices);
        
        // Vérifier la structure des exercices
        foreach ($exercices as $exercice) {
            $this->assertArrayHasKey('nom', $exercice);
            $this->assertArrayHasKey('description', $exercice);
            $this->assertArrayHasKey('inhale', $exercice);
            $this->assertArrayHasKey('hold', $exercice);
            $this->assertArrayHasKey('exhale', $exercice);
            
            $this->assertIsString($exercice['nom']);
            $this->assertIsString($exercice['description']);
            $this->assertIsNumeric($exercice['inhale']);
            $this->assertIsNumeric($exercice['hold']);
            $this->assertIsNumeric($exercice['exhale']);
        }
    }

    /**
     * Test des permissions d'accès aux ressources
     */
    public function testPermissionsRessources()
    {
        // Test accès sans connexion
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
        }
        
        $ressourcesSansConnexion = $this->gestionRessources->recupererRessources(null, null);
        $this->assertIsArray($ressourcesSansConnexion);
        $this->assertEmpty($ressourcesSansConnexion, "Les utilisateurs non connectés ne devraient voir aucune ressource");
    }

    /**
     * Test de performance basique
     */
    public function testPerformanceBasique()
    {
        $start = microtime(true);
        
        // Test de 10 connexions successives
        for ($i = 0; $i < 10; $i++) {
            $database = new Database();
            $conn = $database->getConnection();
            $this->assertNotNull($conn);
        }
        
        $end = microtime(true);
        $duration = $end - $start;
        
        // La création de 10 connexions ne devrait pas prendre plus de 5 secondes
        $this->assertLessThan(5.0, $duration, "Les connexions à la base de données sont trop lentes");
    }
}