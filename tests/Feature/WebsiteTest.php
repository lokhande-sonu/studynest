<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Feature tests for the website public routes.
 */
class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the homepage loads successfully.
     *
     * @return void
     */
    public function test_homepage_loads()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test that the shop page is accessible.
     *
     * @return void
     */
    public function test_shop_page_is_accessible()
    {
        $response = $this->get('/shop');

        $response->assertStatus(200);
    }

    /**
     * Test that the about us page is accessible.
     *
     * @return void
     */
    public function test_about_page_is_accessible()
    {
        $response = $this->get('/about-us');

        $response->assertStatus(200);
    }

    /**
     * Test that the contact us page is accessible.
     *
     * @return void
     */
    public function test_contact_page_is_accessible()
    {
        $response = $this->get('/contact-us');

        $response->assertStatus(200);
    }

    /**
     * Test that the blogs page is accessible.
     *
     * @return void
     */
    public function test_blogs_page_is_accessible()
    {
        $response = $this->get('/blogs');

        $response->assertStatus(200);
    }

    /**
     * Test that the prebooking page is accessible.
     *
     * @return void
     */
    public function test_prebooking_page_is_accessible()
    {
        $response = $this->get('/prebooking');

        $response->assertStatus(200);
    }

    /**
     * Test that terms and conditions page is accessible.
     *
     * @return void
     */
    public function test_terms_page_is_accessible()
    {
        $response = $this->get('/terms-conditions');

        $response->assertStatus(200);
    }

    /**
     * Test that privacy policy page is accessible.
     *
     * @return void
     */
    public function test_privacy_policy_page_is_accessible()
    {
        $response = $this->get('/privacy-policy');

        $response->assertStatus(200);
    }
}
