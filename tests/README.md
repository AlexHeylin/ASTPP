# ASTPP Test Suite

This directory contains unit tests for the ASTPP codebase, specifically focusing on the bug fixes in the cursor branch.

## Setup

1. Install dependencies:
```bash
composer install
```

2. (Optional) Configure database connection for schema validation tests:
```bash
export ASTPP_TEST_DB_DSN="mysql:host=localhost;dbname=astpp_test"
export ASTPP_TEST_DB_USER="root"
export ASTPP_TEST_DB_PASS="password"
```

## Running Tests

Run all tests:
```bash
vendor/bin/phpunit
```

Run specific test suite:
```bash
vendor/bin/phpunit --testsuite "Freeswitch Tests"
vendor/bin/phpunit --testsuite "Database Schema Tests"
```

Run specific test file:
```bash
vendor/bin/phpunit tests/freeswitch/DbTest.php
```

## Test Coverage

### Freeswitch Tests

- **DbTest.php**: Tests for `freeswitch/fs/lib/astpp.db.php`
  - parse_ini_file failure handling
  - Error message handling (not echoing empty errors)
  - run() method return type (array instead of error string)
  - SQL query type detection

- **IndexTest.php**: Tests for `freeswitch/fs/index.php`
  - Undefined index prevention for `$_REQUEST['section']`
  - Path traversal prevention
  - File existence checks before include

- **XmlTest.php**: Tests for `freeswitch/fs/lib/astpp.xml.php`
  - Undefined index prevention for `$_REQUEST['user']` and `$_REQUEST['domain']`
  - Empty result set handling
  - XML output structure validation

- **ConfigurationTest.php**: Tests for `freeswitch/fs/scripts/astpp.configuration.php`
  - Undefined index prevention for `$_REQUEST['key_value']`
  - Valid key_value handling (sofia.conf, acl.conf)
  - Invalid key_value handling

- **CdrTest.php**: Tests for `freeswitch/fs/lib/astpp.cdr.php`
  - Undefined index prevention for array access
  - billsec and hangup_cause handling
  - error_cdr logic

### Database Schema Tests

- **SchemaValidationTest.php**: Database schema validation
  - Field length validation (username, number, email, etc.)
  - Field type validation (varchar, tinyint, mediumtext)
  - Required tables and columns
  - Index validation
  - Foreign key relationships

## Notes

- Schema validation tests require a database connection. They will be skipped if not configured.
- Some tests use mocking/stubs where direct database connections aren't available.
- All tests focus on the bug fixes made in the cursor branch.
