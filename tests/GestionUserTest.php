<?php
// GestionUserTest.php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controleur/gestionUser.php';

class GestionUserTest extends TestCase
{
    public function testAddUser()
    {
        $data = ['username' => 'testUser', 'password' => 'password'];
        $result = addUser($data);
        $this->assertTrue($result);
    }

    public function testModifyUser()
    {
        $data = ['id' => 1, 'username' => 'updatedUser'];
        $result = modifyUser($data);
        $this->assertTrue($result);
    }

    public function testDeleteUser()
    {
        $result = deleteUser(1);
        $this->assertTrue($result);
    }

    public function testListUsers()
    {
        $result = listUsers();
        $this->assertIsArray($result);
    }
}