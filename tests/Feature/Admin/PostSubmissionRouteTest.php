<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Member;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSubmissionRouteTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_resubmitting_a_returned_post_sets_its_status_back_to_submission()
    {
        $admin = $this->makeAdminUser('administrator');
        $category = Category::create(['title' => 'Berita']);
        $member = Member::create([
            'nip' => '199001012020121005',
            'name' => 'Penulis Admin',
            'email' => 'penulisadmin@example.com',
            'agency' => 'Instansi',
            'nomember' => '00004/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
        $post = Post::create([
            'title' => 'Post Yang Dikembalikan',
            'slug' => 'post-yang-dikembalikan',
            'body' => 'Isi',
            'excerpt' => 'Isi',
            'category_id' => $category->id,
            'member_id' => $member->id,
            'status' => 'perlu ada perbaikan',
            'publish_at' => now(),
        ]);

        // This route used to be wired to the cancel() action, which expects
        // a PublicPost id rather than a Post id and threw a 404 here.
        $response = $this->actingAs($admin)->post("/admin/posts/{$post->id}/submission");

        $response->assertRedirect(route('admin.posts.list'));
        $response->assertSessionHas('success');
        $this->assertSame('submission', $post->fresh()->status);
    }
}
