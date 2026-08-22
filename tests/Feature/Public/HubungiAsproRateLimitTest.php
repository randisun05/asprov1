<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubungiAsproRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_contact_form_is_rate_limited()
    {
        $payload = [
            'nip' => '199001012020121050',
            'name' => 'Penanya',
            'email' => 'penanya@example.com',
            'contact' => '081234560050',
            'agency' => 'Instansi',
            'position' => 'Staff',
            'category' => 'Pertanyaan',
            'title' => 'Judul',
            'detail' => 'Detail pertanyaan',
            'code' => 'ABCD',
            'captcha' => 'wrong',
            'term' => '0',
        ];

        for ($i = 0; $i < 6; $i++) {
            $this->post('/hubungi-aspro', $payload);
        }

        $response = $this->post('/hubungi-aspro', $payload);

        $response->assertStatus(429);
    }
}
