<?php
// tests/SimpleTest.php
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testTrueIsTrue()
    {
        $this->assertTrue(true);
    }

    public function testArrayIsArray()
    {
        $array = ['test' => 'value'];
        $this->assertIsArray($array);
    }

    public function testStringIsString()
    {
        $string = "Hello World";
        $this->assertIsString($string);
    }

    public function testPhpVersionIsValid()
    {
        $version = phpversion();
        $this->assertIsString($version);
        $this->assertGreaterThanOrEqual('8.0', $version);
    }

    public function testSessionCanBeStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->assertNotEquals(PHP_SESSION_NONE, session_status());
    }
}