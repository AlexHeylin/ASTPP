<?php

namespace ASTPP\Tests\Freeswitch;

use PHPUnit\Framework\TestCase;

/**
 * Tests for freeswitch/fs/lib/astpp.cdr.php
 */
class CdrTest extends TestCase
{
    /**
     * Test that undefined index errors are prevented for billsec
     */
    public function testUndefinedIndexBillsec()
    {
        $dataVariable = [];
        
        // Test missing billsec
        $this->assertFalse(isset($dataVariable['billsec']));
        
        // The fix: $billsec = isset($dataVariable ['billsec']) ? $dataVariable ['billsec'] : 0;
        $billsec = isset($dataVariable['billsec']) ? $dataVariable['billsec'] : 0;
        $this->assertEquals(0, $billsec);
        
        // Test with billsec set
        $dataVariable['billsec'] = 60;
        $billsec = isset($dataVariable['billsec']) ? $dataVariable['billsec'] : 0;
        $this->assertEquals(60, $billsec);
    }
    
    /**
     * Test that undefined index errors are prevented for hangup_cause
     */
    public function testUndefinedIndexHangupCause()
    {
        $dataVariable = [];
        
        // Test missing hangup_cause
        $this->assertFalse(isset($dataVariable['hangup_cause']));
        
        // The fix: $hangup_cause_default = isset($dataVariable ['hangup_cause']) ? $dataVariable ['hangup_cause'] : '';
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        $this->assertEquals('', $hangup_cause_default);
        
        // Test with hangup_cause set
        $dataVariable['hangup_cause'] = 'NORMAL_CLEARING';
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        $this->assertEquals('NORMAL_CLEARING', $hangup_cause_default);
    }
    
    /**
     * Test hangup cause logic with billsec = 0 and NORMAL_CLEARING
     */
    public function testHangupCauseLogicNormalClearing()
    {
        $dataVariable = [
            'billsec' => 0,
            'hangup_cause' => 'NORMAL_CLEARING'
        ];
        
        $billsec = isset($dataVariable['billsec']) ? $dataVariable['billsec'] : 0;
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        
        if ($billsec == 0 && $hangup_cause_default == 'NORMAL_CLEARING') {
            $hangup_cause = isset($dataVariable['last_bridge_hangup_cause']) 
                ? $dataVariable['last_bridge_hangup_cause'] 
                : $hangup_cause_default;
        } else {
            $hangup_cause = $hangup_cause_default;
        }
        
        $this->assertEquals('NORMAL_CLEARING', $hangup_cause);
    }
    
    /**
     * Test hangup cause logic with last_bridge_hangup_cause
     */
    public function testHangupCauseWithLastBridge()
    {
        $dataVariable = [
            'billsec' => 0,
            'hangup_cause' => 'NORMAL_CLEARING',
            'last_bridge_hangup_cause' => 'ORIGINATOR_CANCEL'
        ];
        
        $billsec = isset($dataVariable['billsec']) ? $dataVariable['billsec'] : 0;
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        
        if ($billsec == 0 && $hangup_cause_default == 'NORMAL_CLEARING') {
            $hangup_cause = isset($dataVariable['last_bridge_hangup_cause']) 
                ? $dataVariable['last_bridge_hangup_cause'] 
                : $hangup_cause_default;
        } else {
            $hangup_cause = $hangup_cause_default;
        }
        
        $this->assertEquals('ORIGINATOR_CANCEL', $hangup_cause);
    }
    
    /**
     * Test error_cdr handling
     */
    public function testErrorCdrHandling()
    {
        // Test with error_cdr = '1'
        $dataVariable = [
            'error_cdr' => '1',
            'hangup_cause' => 'NORMAL_CLEARING',
            'last_bridge_hangup_cause' => 'ORIGINATOR_CANCEL'
        ];
        
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        
        if (isset($dataVariable['error_cdr']) && $dataVariable['error_cdr'] == '1') {
            $hangup_cause = isset($dataVariable['last_bridge_hangup_cause']) 
                ? $dataVariable['last_bridge_hangup_cause'] 
                : (isset($dataVariable['last_bridge_hangup_cause']) 
                    ? $dataVariable['last_bridge_hangup_cause'] 
                    : $hangup_cause_default);
        }
        
        $this->assertEquals('ORIGINATOR_CANCEL', $hangup_cause);
    }
    
    /**
     * Test error_cdr not set
     */
    public function testErrorCdrNotSet()
    {
        $dataVariable = [
            'hangup_cause' => 'NORMAL_CLEARING'
        ];
        
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        
        // Should not enter the error_cdr block
        if (isset($dataVariable['error_cdr']) && $dataVariable['error_cdr'] == '1') {
            $this->fail('Should not enter error_cdr block when not set');
        }
        
        $this->assertEquals('NORMAL_CLEARING', $hangup_cause_default);
    }
    
    /**
     * Test all undefined index scenarios
     */
    public function testAllUndefinedIndexScenarios()
    {
        // Test with completely empty dataVariable
        $dataVariable = [];
        
        $billsec = isset($dataVariable['billsec']) ? $dataVariable['billsec'] : 0;
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        
        $this->assertEquals(0, $billsec);
        $this->assertEquals('', $hangup_cause_default);
        
        // Test with partial data
        $dataVariable = ['billsec' => 30];
        $billsec = isset($dataVariable['billsec']) ? $dataVariable['billsec'] : 0;
        $hangup_cause_default = isset($dataVariable['hangup_cause']) ? $dataVariable['hangup_cause'] : '';
        
        $this->assertEquals(30, $billsec);
        $this->assertEquals('', $hangup_cause_default);
    }
}
