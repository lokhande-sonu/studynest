<?php
/**
 * START: imohitmehto | 2026-08-25 | FIX: Removed CreatesApplication trait (removed in newer Laravel)
 * Replaced with manual createApplication() method for PHP 8.4 compatibility.
 * The trait was removed from Laravel but the test base class still referenced it.
 * END: imohitmehto | FIX: TestCase PHP 8.4 compatibility
 */

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base test case class for Study Nest.
 *
 * Provides common testing functionality for all test cases.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     */
    public function createApplication(): \Illuminate\Contracts\Foundation\Application
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    /**
     * Set up common test fixtures.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear down after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
