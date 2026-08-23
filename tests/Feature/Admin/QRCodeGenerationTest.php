<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QRCodeGenerationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMemberWithPosition(): array
    {
        $member = Member::create([
            'nip' => '199001012020121005',
            'name' => 'Anggota QR',
            'email' => 'anggotaqr@example.com',
            'agency' => 'Instansi',
            'nomember' => '00004/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);

        $main = ProfileDataMain::create([
            'nip' => $member->nip,
            'nomember' => $member->nomember,
            'name' => $member->name,
            'email' => $member->email,
            'contact' => '081234567893',
        ]);

        $position = ProfileDataPosition::create([
            'main_id' => $main->id,
            'agency' => 'Instansi',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ]);

        return [$member, $position];
    }

    // NOTE: PNG rendering (F9WebLtd\QrCode's png backend) requires the
    // imagick PHP extension, which isn't installed in this sandbox, so the
    // happy paths that actually render a QR code can't be exercised here.
    // The route-method fix (GET -> POST) is instead verified via
    // route:list / by confirming the request reaches the controller
    // instead of bouncing off a 404/405 at the routing layer.

    public function test_generate_qr_code_is_registered_as_post()
    {
        $admin = $this->makeAdminUser('administrator');
        [$member] = $this->makeMemberWithPosition();

        // A GET to this endpoint must not succeed (it used to be GET-only,
        // which is the bug: the frontend always POSTs here).
        $this->actingAs($admin)->get('/admin/generate-qr')->assertMethodNotAllowed();
    }

    public function test_generate_qr_code_returns_404_for_an_unknown_nomember()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/generate-qr', [
            'text' => 'tidak-ada',
        ]);

        $response->assertStatus(404);
    }

    public function test_generate_qr_code1_returns_404_instead_of_crashing_for_an_unknown_id()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->get('/admin/members/qrcode/999999');

        $response->assertStatus(404);
    }
}
