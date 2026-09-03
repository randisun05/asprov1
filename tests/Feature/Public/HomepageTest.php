<?php

namespace Tests\Feature\Public;

use App\Models\Event;
use App\Models\Management;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_the_inertia_vue_page()
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('Public/Website/Index'));
    }

    public function test_homepage_shows_the_latest_events()
    {
        $event = Event::create([
            'title' => 'Workshop SDMA',
            'slug' => 'workshop-sdma',
            'body' => 'Deskripsi kegiatan',
            'date' => now()->toDateString(),
            'enddate' => now()->addDay()->toDateString(),
            'place' => 'Jakarta',
            'link' => 'https://zoom.example/workshop',
            'participant' => 100,
            'status' => 'active',
            'file' => 'N',
            'absen' => 'N',
            'category' => 'Kombel',
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('Public/Website/Index')
            ->where('events.0.id', $event->id)
            ->where('events.0.title', 'Workshop SDMA')
        );
    }

    public function test_homepage_excludes_the_media_event()
    {
        Event::create([
            'title' => 'media',
            'slug' => 'media',
            'body' => 'x',
            'date' => now()->toDateString(),
            'enddate' => now()->toDateString(),
            'place' => 'x',
            'link' => 'x',
            'participant' => 1,
            'status' => 'active',
            'file' => 'N',
            'absen' => 'N',
            'category' => 'Kombel',
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('Public/Website/Index')
            ->where('events', [])
        );
    }

    public function test_homepage_reports_registration_stats_by_position()
    {
        Registration::create([
            'nip' => '199001012020121001', 'name' => 'A', 'email' => 'a@example.com',
            'contact' => '081234560001', 'agency' => 'Instansi A', 'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama', 'status' => 'approved',
        ]);
        Registration::create([
            'nip' => '199001012020121002', 'name' => 'B', 'email' => 'b@example.com',
            'contact' => '081234560002', 'agency' => 'Instansi B', 'position' => 'Pranata SDM Aparatur',
            'level' => 'Ahli Pertama', 'status' => 'approved',
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('Public/Website/Index')
            ->where('analisdone', 1)
            ->where('pranatadone', 1)
            ->where('agencydone', 2)
        );
    }

    public function test_homepage_shows_only_active_popup_announcements()
    {
        Management::create(['item' => 'popup', 'status' => '1', 'image' => 'popup-active.jpg', 'link' => '/berita', 'button' => '1']);
        Management::create(['item' => 'popup', 'status' => '0', 'image' => 'popup-inactive.jpg']);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('Public/Website/Index')
            ->has('datas', 1)
            ->where('datas.0.image', 'popup-active.jpg')
        );
    }
}
