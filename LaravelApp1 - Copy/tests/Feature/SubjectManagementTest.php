<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_subjects_page_returns_successful_response(): void
    {
        $response = $this->get('/subjects');

        $response->assertStatus(200);
    }
}
