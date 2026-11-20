<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_home_page_shows_a_public_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()->assertSee('Jelajahi Hunian')->assertSee(route('login'));
    }
}
