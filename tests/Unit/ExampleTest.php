<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit tests for basic application functionality.
 */
class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_application_is_functional()
    {
        $response = $this->get('/');

        // The homepage should return a valid response
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * Test that the application environment is set correctly.
     *
     * @return void
     */
    public function test_application_environment()
    {
        $this->assertEquals('testing', app()->environment());
    }
}
