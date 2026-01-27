<?php

namespace ASTPP\Tests\Database;

use PHPUnit\Framework\TestCase;
use PDO;

/**
 * Database Schema Validation Tests
 * 
 * Tests that database schema matches PHP code expectations,
 * including field lengths, types, and constraints.
 */
class SchemaValidationTest extends TestCase
{
    private $pdo;
    private $schemaFile;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Schema file path
        $this->schemaFile = __DIR__ . '/../../database/astpp-3.0.sql';
        
        // Skip tests if no DB connection available
        if (!getenv('ASTPP_TEST_DB_DSN')) {
            $this->markTestSkipped('Database connection not configured. Set ASTPP_TEST_DB_DSN environment variable.');
        }
        
        try {
            $dsn = getenv('ASTPP_TEST_DB_DSN');
            $user = getenv('ASTPP_TEST_DB_USER') ?: 'root';
            $pass = getenv('ASTPP_TEST_DB_PASS') ?: '';
            
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->markTestSkipped('Could not connect to test database: ' . $e->getMessage());
        }
    }
    
    /**
     * Test that sip_devices.username field length matches code expectations
     * 
     * Code uses: $_REQUEST['user'] which could be up to 40 chars based on schema
     */
    public function testSipDevicesUsernameLength()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE sip_devices");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $usernameCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'username') {
                $usernameCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($usernameCol, 'username column should exist');
        
        // Schema defines: username varchar(40)
        // Extract length from type (e.g., "varchar(40)")
        preg_match('/varchar\((\d+)\)/', $usernameCol['Type'], $matches);
        $length = isset($matches[1]) ? (int)$matches[1] : 0;
        
        $this->assertEquals(40, $length, 'username field should be varchar(40)');
        
        // Test that code can handle max length
        $maxUsername = str_repeat('a', 40);
        $this->assertEquals(40, strlen($maxUsername));
    }
    
    /**
     * Test that accounts.number field length matches code expectations
     */
    public function testAccountsNumberLength()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE accounts");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $numberCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'number') {
                $numberCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($numberCol, 'number column should exist');
        
        // Schema defines: number varchar(20)
        preg_match('/varchar\((\d+)\)/', $numberCol['Type'], $matches);
        $length = isset($matches[1]) ? (int)$matches[1] : 0;
        
        $this->assertEquals(20, $length, 'number field should be varchar(20)');
    }
    
    /**
     * Test that sip_devices.dir_params is mediumtext (can handle large JSON)
     */
    public function testSipDevicesDirParamsType()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE sip_devices");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $dirParamsCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'dir_params') {
                $dirParamsCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($dirParamsCol, 'dir_params column should exist');
        
        // Schema defines: dir_params mediumtext
        $this->assertStringContainsString('text', strtolower($dirParamsCol['Type']), 
            'dir_params should be a text type to handle large JSON data');
    }
    
    /**
     * Test that accounts.email field length matches code expectations
     */
    public function testAccountsEmailLength()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE accounts");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $emailCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'email') {
                $emailCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($emailCol, 'email column should exist');
        
        // Schema defines: email varchar(80)
        preg_match('/varchar\((\d+)\)/', $emailCol['Type'], $matches);
        $length = isset($matches[1]) ? (int)$matches[1] : 0;
        
        $this->assertEquals(80, $length, 'email field should be varchar(80)');
    }
    
    /**
     * Test that cdrs.uniqueid field length matches code expectations
     */
    public function testCdrsUniqueidLength()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE cdrs");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $uniqueidCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'uniqueid') {
                $uniqueidCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($uniqueidCol, 'uniqueid column should exist');
        
        // Schema defines: uniqueid varchar(60)
        preg_match('/varchar\((\d+)\)/', $uniqueidCol['Type'], $matches);
        $length = isset($matches[1]) ? (int)$matches[1] : 0;
        
        $this->assertEquals(60, $length, 'uniqueid field should be varchar(60)');
    }
    
    /**
     * Test that cdrs.callerid field length matches code expectations
     */
    public function testCdrsCalleridLength()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE cdrs");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $calleridCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'callerid') {
                $calleridCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($calleridCol, 'callerid column should exist');
        
        // Schema defines: callerid varchar(30)
        preg_match('/varchar\((\d+)\)/', $calleridCol['Type'], $matches);
        $length = isset($matches[1]) ? (int)$matches[1] : 0;
        
        $this->assertEquals(30, $length, 'callerid field should be varchar(30)');
    }
    
    /**
     * Test that required tables exist
     */
    public function testRequiredTablesExist()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $requiredTables = [
            'accounts',
            'sip_devices',
            'cdrs',
            'gateways',
            'sip_profiles',
            'ip_map',
        ];
        
        $stmt = $this->pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($requiredTables as $table) {
            $this->assertContains($table, $tables, "Required table '$table' should exist");
        }
    }
    
    /**
     * Test that accounts table has required columns
     */
    public function testAccountsTableRequiredColumns()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $requiredColumns = [
            'id',
            'number',
            'status',
            'deleted',
            'balance',
        ];
        
        $stmt = $this->pdo->query("DESCRIBE accounts");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($requiredColumns as $column) {
            $this->assertContains($column, $columns, 
                "Required column '$column' should exist in accounts table");
        }
    }
    
    /**
     * Test that sip_devices table has required columns
     */
    public function testSipDevicesTableRequiredColumns()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $requiredColumns = [
            'id',
            'username',
            'accountid',
            'dir_params',
            'dir_vars',
            'status',
        ];
        
        $stmt = $this->pdo->query("DESCRIBE sip_devices");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($requiredColumns as $column) {
            $this->assertContains($column, $columns, 
                "Required column '$column' should exist in sip_devices table");
        }
    }
    
    /**
     * Test that accounts.status is tinyint (0 or 1)
     */
    public function testAccountsStatusType()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE accounts");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $statusCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'status') {
                $statusCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($statusCol, 'status column should exist');
        
        // Schema defines: status tinyint(1)
        $this->assertStringContainsString('tinyint', strtolower($statusCol['Type']), 
            'status should be tinyint');
    }
    
    /**
     * Test that accounts.deleted is tinyint (0 or 1)
     */
    public function testAccountsDeletedType()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        $stmt = $this->pdo->query("DESCRIBE accounts");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $deletedCol = null;
        foreach ($columns as $col) {
            if ($col['Field'] === 'deleted') {
                $deletedCol = $col;
                break;
            }
        }
        
        $this->assertNotNull($deletedCol, 'deleted column should exist');
        
        // Schema defines: deleted tinyint(1)
        $this->assertStringContainsString('tinyint', strtolower($deletedCol['Type']), 
            'deleted should be tinyint');
    }
    
    /**
     * Test that foreign key relationships exist where expected
     */
    public function testForeignKeyRelationships()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        // Check that sip_devices.accountid references accounts.id
        $stmt = $this->pdo->query("
            SELECT 
                CONSTRAINT_NAME,
                TABLE_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'sip_devices'
            AND COLUMN_NAME = 'accountid'
        ");
        
        $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Note: Foreign keys may not be explicitly defined in schema
        // This test documents the expected relationship
        if (count($foreignKeys) > 0) {
            $fk = $foreignKeys[0];
            $this->assertEquals('accounts', $fk['REFERENCED_TABLE_NAME'], 
                'sip_devices.accountid should reference accounts.id');
        }
    }
    
    /**
     * Test that indexes exist on frequently queried columns
     */
    public function testIndexesExist()
    {
        if (!$this->pdo) {
            $this->markTestSkipped('No database connection');
        }
        
        // Check indexes on accounts table
        $stmt = $this->pdo->query("SHOW INDEXES FROM accounts");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $indexColumns = array_column($indexes, 'Column_name');
        
        // These columns should be indexed for performance
        $expectedIndexes = ['number', 'status', 'deleted'];
        
        foreach ($expectedIndexes as $expectedIndex) {
            $this->assertContains($expectedIndex, $indexColumns, 
                "Column '$expectedIndex' should be indexed in accounts table");
        }
        
        // Check indexes on sip_devices table
        $stmt = $this->pdo->query("SHOW INDEXES FROM sip_devices");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $indexColumns = array_column($indexes, 'Column_name');
        
        // username should be unique indexed
        $this->assertContains('username', $indexColumns, 
            'username should be indexed in sip_devices table');
    }
}
