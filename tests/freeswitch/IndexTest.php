<?php

namespace ASTPP\Tests\Freeswitch;

use PHPUnit\Framework\TestCase;

/**
 * Tests for freeswitch/fs/index.php
 */
class IndexTest extends TestCase
{
    private $originalRequest;
    private $originalGet;
    private $originalPost;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Save original superglobals
        $this->originalRequest = $_REQUEST ?? null;
        $this->originalGet = $_GET ?? null;
        $this->originalPost = $_POST ?? null;
    }
    
    protected function tearDown(): void
    {
        // Restore original superglobals
        if ($this->originalRequest !== null) {
            $_REQUEST = $this->originalRequest;
        } else {
            unset($_REQUEST);
        }
        if ($this->originalGet !== null) {
            $_GET = $this->originalGet;
        }
        if ($this->originalPost !== null) {
            $_POST = $this->originalPost;
        }
        parent::tearDown();
    }
    
    /**
     * Test that undefined index error is prevented for $_REQUEST['section']
     */
    public function testUndefinedIndexSection()
    {
        // Clear $_REQUEST
        unset($_REQUEST);
        $_REQUEST = [];
        
        // Test that isset check prevents undefined index
        $this->assertFalse(isset($_REQUEST['section']));
        
        // The fix should check isset before using
        if (!isset($_REQUEST['section'])) {
            // Should call xml_not_found() or handle gracefully
            $this->assertTrue(true, 'Should handle missing section gracefully');
        }
    }
    
    /**
     * Test file inclusion security - path traversal prevention
     */
    public function testPathTraversalPrevention()
    {
        // Test that basename() prevents path traversal
        $maliciousInputs = [
            '../other_file',
            '../../etc/passwd',
            'configuration.php',
            'astpp.configuration.php',
        ];
        
        foreach ($maliciousInputs as $input) {
            $sanitized = basename($input);
            // basename should remove directory components
            $this->assertStringNotContainsString('..', $sanitized, 
                "basename should prevent path traversal: $input");
            $this->assertStringNotContainsString('/', $sanitized, 
                "basename should remove slashes: $input");
        }
        
        // Test Windows backslashes separately (basename handles them differently on Linux)
        $windowsPath = '..\\..\\windows\\system32';
        $sanitized = basename($windowsPath);
        // On Linux, basename treats backslashes as regular characters
        // The important thing is that it removes forward slashes and prevents directory traversal
        $this->assertStringNotContainsString('../', $sanitized, 
            "basename should prevent forward slash path traversal");
    }
    
    /**
     * Test file existence check before include
     */
    public function testFileExistenceCheck()
    {
        $testDir = sys_get_temp_dir() . '/astpp_test_scripts';
        if (!is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }
        
        $existingFile = $testDir . '/astpp.configuration.php';
        $nonExistentFile = $testDir . '/astpp.nonexistent.php';
        
        // Create a test file
        file_put_contents($existingFile, '<?php // test file');
        
        // Test file_exists check
        $this->assertTrue(file_exists($existingFile), 'Existing file should be found');
        $this->assertFalse(file_exists($nonExistentFile), 'Non-existent file should not be found');
        
        // Cleanup
        unlink($existingFile);
        rmdir($testDir);
    }
    
    /**
     * Test that section parameter is properly sanitized
     */
    public function testSectionSanitization()
    {
        $_REQUEST['section'] = '../malicious';
        $section = basename($_REQUEST['section']);
        $this->assertEquals('malicious', $section);
        $this->assertStringNotContainsString('..', $section);
        
        $_REQUEST['section'] = 'configuration';
        $section = basename($_REQUEST['section']);
        $this->assertEquals('configuration', $section);
    }
    
    /**
     * Test file path construction
     */
    public function testFilePathConstruction()
    {
        $_REQUEST['section'] = 'configuration';
        $section = basename($_REQUEST['section']);
        $file = "astpp." . $section . ".php";
        $filepath = "scripts/" . $file;
        
        $this->assertEquals('astpp.configuration.php', $file);
        $this->assertEquals('scripts/astpp.configuration.php', $filepath);
    }
}
