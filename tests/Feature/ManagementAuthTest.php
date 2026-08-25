<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Feature tests for the management authentication system.
 */
class ManagementAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the login page is accessible.
     *
     * @return void
     */
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/management/login');

        $response->assertStatus(200);
    }

    /**
     * Test that unauthenticated users are redirected to login.
     *
     * @return void
     */
    public function test_unauthenticated_users_redirected_to_login()
    {
        $response = $this->get('/management/dashboard');

        $response->assertRedirect('/management/login');
    }
}
