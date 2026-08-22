<?php

namespace Tests\Feature\User;

use App\Models\Member;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileImageUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        $member = Member::create([
            'nip' => '199001012020121005',
            'name' => 'Anggota Foto',
            'email' => 'anggotafoto@example.com',
            'agency' => 'Instansi Lama',
            'nomember' => '00004/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);

        $main = ProfileDataMain::create([
            'nip' => $member->nip,
            'nomember' => $member->nomember,
            'name' => $member->name,
            'email' => $member->email,
            'contact' => '081234567893',
            'image' => 'images/existing-photo.jpg',
        ]);

        ProfileDataPosition::create([
            'main_id' => $main->id,
            'agency' => 'Instansi Lama',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ]);

        return $member;
    }

    public function test_uploading_a_new_photo_updates_the_stored_image()
    {
        Storage::fake('public');

        $member = $this->makeMember();

        $file = UploadedFile::fake()->image('foto-baru.jpg');

        $this->actingAs($member, 'member')->post('/user/profile/image', [
            'image' => $file,
        ]);

        $main = ProfileDataMain::where('nip', $member->nip)->first();
        $this->assertNotSame('images/existing-photo.jpg', $main->image);
        $this->assertNotNull($main->image);
    }

    public function test_submitting_without_a_file_keeps_the_existing_photo()
    {
        $member = $this->makeMember();

        $this->actingAs($member, 'member')->post('/user/profile/image', []);

        $main = ProfileDataMain::where('nip', $member->nip)->first();
        $this->assertSame('images/existing-photo.jpg', $main->image);
    }
}
