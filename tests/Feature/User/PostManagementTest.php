<?php

namespace Tests\Feature\User;

use App\Models\Category;
use App\Models\Member;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostManagementTest extends TestCase
{
    use RefreshDatabase;

    private static int $memberSequence = 0;

    private function makeMember(): Member
    {
        self::$memberSequence++;

        return Member::create([
            'nip' => '19900101202012' . str_pad((string) self::$memberSequence, 4, '0', STR_PAD_LEFT),
            'name' => 'Penulis ' . self::$memberSequence,
            'email' => 'penulis' . self::$memberSequence . '@example.com',
            'agency' => 'Instansi',
            'nomember' => '0000' . self::$memberSequence . '/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    private function makePost(Member $member, array $overrides = []): Post
    {
        $category = Category::create(['title' => 'Umum ' . uniqid()]);

        return Post::create(array_merge([
            'title' => 'Judul Post ' . uniqid(),
            'slug' => 'judul-post-' . uniqid(),
            'body' => 'Isi cerita',
            'excerpt' => 'Isi cerita',
            'category_id' => $category->id,
            'member_id' => $member->id,
            'status' => 'private',
            'publish_at' => now(),
        ], $overrides));
    }

    public function test_member_can_delete_their_own_post()
    {
        $member = $this->makeMember();
        $post = $this->makePost($member);

        $response = $this->actingAs($member, 'member')->delete("/user/posts/{$post->id}");

        $response->assertRedirect(route('user.posts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_member_cannot_delete_another_members_post()
    {
        $owner = $this->makeMember();
        $intruder = $this->makeMember();
        $post = $this->makePost($owner);

        $response = $this->actingAs($intruder, 'member')->delete("/user/posts/{$post->id}");

        $response->assertRedirect(route('user.posts.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }

    public function test_member_cannot_update_another_members_post()
    {
        $owner = $this->makeMember();
        $intruder = $this->makeMember();
        $post = $this->makePost($owner, ['title' => 'Judul Asli']);

        $response = $this->actingAs($intruder, 'member')->post("/user/posts/{$post->id}", [
            'title' => 'Judul Diganti Paksa',
            'category' => $post->category_id,
            'body' => 'Isi baru',
        ]);

        $response->assertSessionHas('error');
        $this->assertSame('Judul Asli', $post->fresh()->title);
    }

    public function test_member_can_update_their_own_post()
    {
        $member = $this->makeMember();
        $post = $this->makePost($member, ['title' => 'Judul Lama']);

        $response = $this->actingAs($member, 'member')->post("/user/posts/{$post->id}", [
            'title' => 'Judul Baru',
            'category' => $post->category_id,
            'body' => 'Isi baru',
        ]);

        $response->assertRedirect(route('user.posts.index'));
        $response->assertSessionHas('success');
        $this->assertSame('Judul Baru', $post->fresh()->title);
    }

    public function test_creating_a_post_with_a_duplicate_title_fails_validation_instead_of_crashing()
    {
        $member = $this->makeMember();
        $existing = $this->makePost($member, ['title' => 'Judul Yang Sama']);
        $category = Category::first();

        $response = $this->actingAs($member, 'member')->post('/user/posts/', [
            'title' => 'Judul Yang Sama',
            'category' => $existing->category_id,
            'body' => 'Isi cerita lain',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertSame(1, Post::where('title', 'Judul Yang Sama')->count());
    }

    public function test_member_can_submit_their_own_post_for_review()
    {
        $member = $this->makeMember();
        $post = $this->makePost($member, ['status' => 'private']);

        $response = $this->actingAs($member, 'member')->post("/user/posts/{$post->id}/submission");

        $response->assertRedirect(route('user.posts.index'));
        $response->assertSessionHas('success');
        $this->assertSame('submission', $post->fresh()->status);
    }

    public function test_creating_a_post_rejects_a_non_image_file_uploaded_as_the_picture()
    {
        Storage::fake('public');

        $member = $this->makeMember();
        $category = Category::create(['title' => 'Umum ' . uniqid()]);

        // The form field is named "picture" - this used to bypass all
        // validation because the backend validated a field named "image",
        // which the frontend never sends.
        $response = $this->actingAs($member, 'member')->post('/user/posts/', [
            'title' => 'Judul Dengan Gambar Salah',
            'category' => $category->id,
            'body' => 'Isi cerita',
            'picture' => UploadedFile::fake()->create('malicious.php', 10),
        ]);

        $response->assertSessionHasErrors('picture');
        $this->assertDatabaseMissing('posts', ['title' => 'Judul Dengan Gambar Salah']);
    }

    public function test_member_can_view_their_own_post_by_slug()
    {
        $member = $this->makeMember();
        $post = $this->makePost($member, ['slug' => 'cerita-unik-saya']);

        $response = $this->actingAs($member, 'member')->get('/user/posts/cerita-unik-saya');

        $response->assertOk();
    }
}
