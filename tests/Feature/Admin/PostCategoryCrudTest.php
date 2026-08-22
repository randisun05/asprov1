<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Member;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCategoryCrudTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_administrator_can_create_a_category()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/category/store', ['title' => 'Berita']);

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['title' => 'Berita']);
    }

    public function test_administrator_can_edit_a_category()
    {
        $admin = $this->makeAdminUser('administrator');
        $category = Category::create(['title' => 'Lama']);

        $response = $this->actingAs($admin)->put("/admin/category/{$category->id}", ['title' => 'Baru']);

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'title' => 'Baru']);
    }

    public function test_administrator_can_delete_an_unused_category()
    {
        $admin = $this->makeAdminUser('administrator');
        $category = Category::create(['title' => 'Tidak Dipakai']);

        $response = $this->actingAs($admin)->delete("/admin/category/{$category->id}");

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_deleting_a_category_still_used_by_a_post_fails_gracefully()
    {
        $admin = $this->makeAdminUser('administrator');
        $category = Category::create(['title' => 'Dipakai']);
        $member = Member::create([
            'nip' => '199001012020121009',
            'name' => 'Anggota Post',
            'email' => 'anggotapost@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00009/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
        Post::create([
            'title' => 'Judul',
            'slug' => 'judul',
            'body' => 'Isi',
            'excerpt' => 'Isi',
            'category_id' => $category->id,
            'publish_at' => now(),
            'member_id' => $member->id,
            'status' => 'submission',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/category/{$category->id}");

        $response->assertRedirect(route('admin.posts.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
