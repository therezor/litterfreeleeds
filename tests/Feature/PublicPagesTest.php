<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_every_public_page_renders(): void
    {
        $paths = [
            '/', '/upcoming-picks', '/about', '/contact-us', '/privacy-policy',
            '/site-map', '/join', '/purple-bag-conditions',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_home_page_links_to_every_public_page(): void
    {
        $response = $this->get('/');

        foreach (['/upcoming-picks', '/about', '/contact-us', '/privacy-policy', '/site-map', '/join'] as $path) {
            $response->assertSee(url($path));
        }
    }
}
