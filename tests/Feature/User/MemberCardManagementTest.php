<?php

namespace Tests\Feature\User;

use App\Models\Member;
use App\Models\ProfileDataMain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberCardManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        $member = Member::create([
            'nip' => '199001012020121005',
            'name' => 'Anggota Kartu',
            'email' => 'anggotakartu@example.com',
            'agency' => 'Instansi',
            'nomember' => '00004/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);

        ProfileDataMain::create([
            'nip' => $member->nip,
            'nomember' => $member->nomember,
            'name' => $member->name,
            'email' => $member->email,
            'contact' => '081234567893',
            'image' => 'images/existing-card-photo.jpg',
        ]);

        return $member;
    }

    public function test_submitting_without_a_file_keeps_the_existing_card_photo()
    {
        $member = $this->makeMember();

        $this->actingAs($member, 'member')->post('/user/member-card/update', []);

        $main = ProfileDataMain::where('nip', $member->nip)->first();
        $this->assertSame('images/existing-card-photo.jpg', $main->image);
    }

    public function test_uploading_a_new_photo_updates_the_card_image()
    {
        Storage::fake('public');
        $member = $this->makeMember();

        $this->actingAs($member, 'member')->post('/user/member-card/update', [
            'image' => UploadedFile::fake()->image('kartu-baru.jpg'),
        ]);

        $main = ProfileDataMain::where('nip', $member->nip)->first();
        $this->assertNotSame('images/existing-card-photo.jpg', $main->image);
    }

    public function test_save_member_card_requires_authentication()
    {
        // The `member` middleware redirects unauthenticated requests before
        // the controller's own auth check ever runs.
        $response = $this->post('/user/member-card/save-member-card', [
            'image' => 'data:image/png;base64,not-real-data',
        ]);

        $response->assertRedirect('/user/login');
    }

    public function test_save_member_card_rejects_non_image_payloads()
    {
        $member = $this->makeMember();

        $response = $this->actingAs($member, 'member')->postJson('/user/member-card/save-member-card', [
            'image' => 'not-a-data-uri-at-all',
        ]);

        $response->assertStatus(422);
    }

    public function test_save_member_card_stores_the_image_for_the_authenticated_member()
    {
        Storage::fake('public');
        $member = $this->makeMember();

        // A minimal valid 1x1 PNG, base64 encoded.
        $pngBase64 = base64_encode(base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='
        ));

        $response = $this->actingAs($member, 'member')->postJson('/user/member-card/save-member-card', [
            'image' => 'data:image/png;base64,' . $pngBase64,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        Storage::disk('public')->assertExists("member-cards/member-card-{$member->id}.png");
    }
}
