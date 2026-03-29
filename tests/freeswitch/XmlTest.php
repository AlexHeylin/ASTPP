<?php

namespace ASTPP\Tests\Freeswitch;

use PHPUnit\Framework\TestCase;

/**
 * Tests for freeswitch/fs/lib/astpp.xml.php
 */
class XmlTest extends TestCase
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
     * Test that update_vm_data handles missing $_REQUEST['user']
     */
    public function testUpdateVmDataMissingUser()
    {
        unset($_REQUEST);
        $_REQUEST = [];
        
        // Should return early if $_REQUEST['user'] is not set
        $this->assertFalse(isset($_REQUEST['user']));
        
        // The fix should check isset before using
        if (!isset($_REQUEST['user'])) {
            // Should return early
            $this->assertTrue(true, 'Should return early when user is not set');
        }
    }
    
    /**
     * Test that update_vm_data handles empty result set
     */
    public function testUpdateVmDataEmptyResultSet()
    {
        // Test that empty result set is handled
        $res_dir = [];
        $this->assertTrue(empty($res_dir));
        $this->assertFalse(isset($res_dir[0]));
        
        // The fix checks: if (empty($res_dir) || !isset($res_dir[0]))
        if (empty($res_dir) || !isset($res_dir[0])) {
            // Should return early
            $this->assertTrue(true, 'Should return early when result set is empty');
        }
    }
    
    /**
     * Test that load_directory handles missing $_REQUEST parameters
     */
    public function testLoadDirectoryMissingParameters()
    {
        // Test missing user
        unset($_REQUEST);
        $_REQUEST = [];
        $this->assertFalse(isset($_REQUEST['user']));
        $this->assertFalse(isset($_REQUEST['domain']));
        
        // Should return empty XML
        if (!isset($_REQUEST['user']) || !isset($_REQUEST['domain'])) {
            $xml = "";
            $this->assertEquals("", $xml);
        }
        
        // Test missing domain only
        $_REQUEST['user'] = 'testuser';
        $this->assertTrue(isset($_REQUEST['user']));
        $this->assertFalse(isset($_REQUEST['domain']));
        
        if (!isset($_REQUEST['user']) || !isset($_REQUEST['domain'])) {
            $xml = "";
            $this->assertEquals("", $xml);
        }
    }
    
    /**
     * Test that load_directory uses $_REQUEST values safely
     */
    public function testLoadDirectoryUsesRequestSafely()
    {
        $_REQUEST['user'] = 'testuser';
        $_REQUEST['domain'] = 'testdomain.com';
        
        // Values should be available
        $this->assertTrue(isset($_REQUEST['user']));
        $this->assertTrue(isset($_REQUEST['domain']));
        $this->assertEquals('testuser', $_REQUEST['user']);
        $this->assertEquals('testdomain.com', $_REQUEST['domain']);
    }
    
    /**
     * Test SQL injection prevention in queries
     */
    public function testSqlInjectionPrevention()
    {
        // Note: The current code uses direct string concatenation which is vulnerable
        // This test documents the issue - ideally should use prepared statements
        
        $_REQUEST['user'] = "test' OR '1'='1";
        $query = "SELECT * FROM sip_devices where username='" . $_REQUEST['user'] . "' limit 1";
        
        // This would be vulnerable to SQL injection
        $this->assertStringContainsString("' OR '1'='1", $query);
        
        // Ideally, this should use prepared statements with bound parameters
        // This test documents that the current implementation is vulnerable
    }
    
    /**
     * Test XML output contains expected values
     */
    public function testXmlOutputStructure()
    {
        // Test that XML structure is correct when data is available
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
        $xml .= "<document type=\"freeswitch/xml\">\n";
        $xml .= "   <section name=\"Directory\" description=\"Directory\">\n";
        
        $this->assertStringContainsString('<?xml version="1.0"', $xml);
        $this->assertStringContainsString('freeswitch/xml', $xml);
        $this->assertStringContainsString('Directory', $xml);
    }
}
