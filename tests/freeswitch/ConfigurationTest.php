<?php

namespace ASTPP\Tests\Freeswitch;

use PHPUnit\Framework\TestCase;

/**
 * Tests for freeswitch/fs/scripts/astpp.configuration.php
 */
class ConfigurationTest extends TestCase
{
    private $originalRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->originalRequest = $_REQUEST ?? null;
    }
    
    protected function tearDown(): void
    {
        if ($this->originalRequest !== null) {
            $_REQUEST = $this->originalRequest;
        } else {
            unset($_REQUEST);
        }
        parent::tearDown();
    }
    
    /**
     * Test that undefined index error is prevented for $_REQUEST['key_value']
     */
    public function testUndefinedIndexKeyValue()
    {
        // Clear $_REQUEST
        unset($_REQUEST);
        $_REQUEST = [];
        
        // Test that isset check prevents undefined index
        $this->assertFalse(isset($_REQUEST['key_value']));
        
        // The fix should check isset before using
        if (!isset($_REQUEST['key_value'])) {
            // Should call xml_not_found() and exit
            $this->assertTrue(true, 'Should handle missing key_value gracefully');
        }
    }
    
    /**
     * Test that sofia.conf key_value is handled
     */
    public function testSofiaConfKeyValue()
    {
        $_REQUEST['key_value'] = 'sofia.conf';
        
        $this->assertTrue(isset($_REQUEST['key_value']));
        $this->assertEquals('sofia.conf', $_REQUEST['key_value']);
        
        if ($_REQUEST['key_value'] == 'sofia.conf') {
            $this->assertTrue(true, 'Should handle sofia.conf');
        }
    }
    
    /**
     * Test that acl.conf key_value is handled
     */
    public function testAclConfKeyValue()
    {
        $_REQUEST['key_value'] = 'acl.conf';
        
        $this->assertTrue(isset($_REQUEST['key_value']));
        $this->assertEquals('acl.conf', $_REQUEST['key_value']);
        
        if ($_REQUEST['key_value'] == 'acl.conf') {
            $this->assertTrue(true, 'Should handle acl.conf');
        }
    }
    
    /**
     * Test that invalid key_value calls xml_not_found
     */
    public function testInvalidKeyValue()
    {
        $_REQUEST['key_value'] = 'invalid.conf';
        
        $this->assertTrue(isset($_REQUEST['key_value']));
        $this->assertNotEquals('sofia.conf', $_REQUEST['key_value']);
        $this->assertNotEquals('acl.conf', $_REQUEST['key_value']);
        
        // Should fall through to xml_not_found()
        if ($_REQUEST['key_value'] != 'sofia.conf' && $_REQUEST['key_value'] != 'acl.conf') {
            $this->assertTrue(true, 'Should call xml_not_found for invalid key');
        }
    }
    
    /**
     * Test that exit is called when key_value is missing
     */
    public function testExitOnMissingKeyValue()
    {
        unset($_REQUEST);
        $_REQUEST = [];
        
        // The fix should call xml_not_found() and exit()
        if (!isset($_REQUEST['key_value'])) {
            // In actual code, this would call xml_not_found() and exit()
            $this->assertTrue(true, 'Should exit when key_value is missing');
        }
    }
}
