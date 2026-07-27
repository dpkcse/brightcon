<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    public function test_custom_404_renders_without_shared_site_settings(): void
    {
        $response = $this->get('/a-page-that-does-not-exist');

        $response->assertNotFound()->assertSee('Page Not Found');
    }
}
